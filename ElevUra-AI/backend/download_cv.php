<?php
/**
 * ElevUra — download a saved CV PDF (authenticated owner only)
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

$userId = !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$cvId = (int) ($_GET['id'] ?? 0);

if ($userId <= 0) {
    http_response_code(401);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Authentication required.';
    exit;
}

if ($cvId <= 0) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Invalid CV id.';
    exit;
}

try {
    $stmt = get_db()->prepare(
        'SELECT cv_title, file_path FROM cvs WHERE id = ? AND user_id = ? LIMIT 1'
    );
    $stmt->execute([$cvId, $userId]);
    $row = $stmt->fetch();
} catch (PDOException $e) {
    error_log('[CVWriter] Download lookup failed: ' . $e->getMessage());
    http_response_code(500);
    exit;
}

if (!$row || empty($row['file_path'])) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'CV not found.';
    exit;
}

$relativePath = (string) $row['file_path'];
if (str_contains($relativePath, '..')) {
    http_response_code(400);
    exit;
}

$fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

if (!is_file($fullPath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'PDF file not found.';
    exit;
}

$safeName = preg_replace('/[^a-zA-Z0-9._-]+/', '-', (string) $row['cv_title']) ?? 'resume';
$safeName = trim($safeName, '-') ?: 'resume';

header('Content-Type: application/pdf');
header('Content-Length: ' . (string) filesize($fullPath));
header('Content-Disposition: attachment; filename="' . $safeName . '.pdf"');
header('X-Content-Type-Options: nosniff');

readfile($fullPath);
exit;
