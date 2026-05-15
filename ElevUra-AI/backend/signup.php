<?php
/**
 * ElevUra — register new user
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed.', 405);
}

$data = read_json_body();
$username = trim((string) ($data['username'] ?? ''));
$email    = trim((string) ($data['email'] ?? ''));
$password = (string) ($data['password'] ?? '');
$confirm  = (string) ($data['confirm_password'] ?? $data['confirm'] ?? '');

if ($username === '' || $email === '' || $password === '' || $confirm === '') {
    json_error('All fields are required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_error('Enter a valid email address.');
}

if (!preg_match('/^[a-zA-Z0-9_]{2,50}$/', $username)) {
    json_error('Username must be 2–50 characters (letters, numbers, underscore).');
}

if (strlen($password) < 8) {
    json_error('Password must be at least 8 characters.');
}

if ($password !== $confirm) {
    json_error('Passwords do not match.');
}

try {
    $pdo = get_db();

    $check = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
    $check->execute([$email, $username]);
    if ($check->fetch()) {
        json_error('An account with this email or username already exists.', 409);
    }

    $hash   = password_hash($password, PASSWORD_DEFAULT);
    $avatar = pick_default_avatar($username);

    $insert = $pdo->prepare(
        'INSERT INTO users (username, email, password_hash, avatar, membership_tier)
         VALUES (?, ?, ?, ?, ?)'
    );
    $insert->execute([$username, $email, $hash, $avatar, 'Free']);

    $userId = (int) $pdo->lastInsertId();
    $userRow = fetch_user_by_id($userId);
    if (!$userRow) {
        json_error('Account created but session could not start.', 500);
    }

    $payload = login_user_session($userRow);

    json_response([
        'success' => true,
        'message' => 'Account created successfully.',
        'user'    => $payload,
    ], 201);
} catch (PDOException $e) {
    json_error('Database connection failed. Import elevura.sql and start MySQL in XAMPP.', 500);
}
