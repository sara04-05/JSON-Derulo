<?php
/**
 * ElevUra — persist generated CV PDF to storage and database
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

$userId = require_auth();
$input = read_json_body();

$cvTitle = trim((string) ($input['cv_title'] ?? ''));
$pdfBase64 = (string) ($input['pdf_base64'] ?? '');
$exportType = trim((string) ($input['export_type'] ?? 'ats'));

if ($cvTitle === '') {
    $cvTitle = 'Resume';
}

if ($pdfBase64 === '') {
    json_error('PDF data is required.');
}

if (str_contains($pdfBase64, ',')) {
    $pdfBase64 = (string) substr($pdfBase64, (int) strpos($pdfBase64, ',') + 1);
}

$pdfBytes = base64_decode($pdfBase64, true);
if ($pdfBytes === false || !str_starts_with($pdfBytes, '%PDF')) {
    json_error('Invalid PDF file.');
}

$maxBytes = 8 * 1024 * 1024;
if (strlen($pdfBytes) > $maxBytes) {
    json_error('PDF file is too large (max 8 MB).');
}

$uploadDir = dirname(__DIR__) . '/uploads/cvs';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
    json_error('Could not create upload directory.', 500);
}

$slug = preg_replace('/[^a-zA-Z0-9_-]+/', '-', $cvTitle) ?? '';
$slug = trim((string) $slug, '-');
if ($slug === '') {
    $slug = 'resume';
}

$filename = sprintf(
    'user_%d_%s_%s.pdf',
    $userId,
    date('Ymd_His'),
    substr($slug, 0, 48)
);

$absolutePath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
if (file_put_contents($absolutePath, $pdfBytes) === false) {
    json_error('Could not save PDF file.', 500);
}

$relativePath = 'uploads/cvs/' . $filename;

try {
    $pdo = get_db();
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `cvs` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `cv_title` VARCHAR(255) NOT NULL,
            `ats_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `file_path` VARCHAR(512) DEFAULT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_cvs_user_id` (`user_id`),
            KEY `idx_cvs_ats_score` (`ats_score`),
            CONSTRAINT `fk_cvs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $stmt = $pdo->prepare(
        'INSERT INTO cvs (user_id, cv_title, ats_score, file_path) VALUES (?, ?, 0, ?)'
    );
    $stmt->execute([$userId, mb_substr($cvTitle, 0, 255), $relativePath]);
    $cvId = (int) $pdo->lastInsertId();
} catch (PDOException $e) {
    @unlink($absolutePath);
    error_log('[CVWriter] Failed to save CV record for user ' . $userId . ': ' . $e->getMessage());
    json_error('Could not save CV to database.', 500);
}

json_response([
    'success'    => true,
    'cv_id'      => $cvId,
    'cv_title'   => $cvTitle,
    'file_path'  => $relativePath,
    'export_type'=> $exportType,
]);
