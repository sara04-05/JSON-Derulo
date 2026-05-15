<?php
/**
 * ElevUra — login (email or username + password)
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed.', 405);
}

$data = read_json_body();
$identifier = trim((string) ($data['identifier'] ?? $data['email'] ?? ''));
$password   = (string) ($data['password'] ?? '');

if ($identifier === '' || $password === '') {
    json_error('Email/username and password are required.');
}

try {
    $pdo = get_db();
    $stmt = $pdo->prepare(
        'SELECT id, username, email, password_hash, avatar, membership_tier
         FROM users
         WHERE email = ? OR username = ?
         LIMIT 1'
    );
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        json_error('Invalid credentials. Check your email/username and password.', 401);
    }

    $payload = login_user_session($user);

    json_response([
        'success' => true,
        'message' => 'Signed in successfully.',
        'user'    => $payload,
    ]);
} catch (PDOException $e) {
    json_error('Database connection failed. Import elevura.sql and start MySQL in XAMPP.', 500);
}
