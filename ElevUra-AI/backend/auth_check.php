<?php
/**
 * ElevUra — session auth helpers (include from protected endpoints)
 */
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/db.php';

function current_user_id(): ?int
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    return (int) $_SESSION['user_id'];
}

function require_auth(): int
{
    $id = current_user_id();
    if (!$id) {
        json_error('Authentication required.', 401);
    }
    return $id;
}

function fetch_user_by_id(int $userId): ?array
{
    $stmt = get_db()->prepare(
        'SELECT id, username, email, avatar, membership_tier, created_at
         FROM users WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function login_user_session(array $userRow): array
{
    $_SESSION['user_id'] = (int) $userRow['id'];
    $_SESSION['username'] = $userRow['username'];
    return format_user_row($userRow);
}
