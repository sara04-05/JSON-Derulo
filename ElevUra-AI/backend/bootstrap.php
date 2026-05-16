<?php
/**
 * ElevUra — shared bootstrap: session, JSON helpers, CORS-safe same-origin API
 */
declare(strict_types=1);

require_once __DIR__ . '/config.php';

// Load .env into environment (server-side only) and provide app_env helper
if (!function_exists('app_load_env_file')) {
    function app_load_env_file(?string $path = null): void
    {
        $root = dirname(__DIR__);
        $path = $path ?? $root . '/.env';
        if (!is_file($path)) {
            return;
        }

        $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!is_array($lines)) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;
            if (strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $k = trim($k);
            $v = trim($v);
            $v = preg_replace('/^\"(.*)\"$/', '$1', $v);
            $v = preg_replace('/^\'(.*)\'$/', '$1', $v);
            if (getenv($k) === false) {
                putenv($k . '=' . $v);
            }
            if (!isset($_ENV[$k])) $_ENV[$k] = $v;
            if (!isset($_SERVER[$k])) $_SERVER[$k] = $v;
        }
    }
}

if (!function_exists('app_env')) {
    function app_env(string $key, $default = null)
    {
        $val = getenv($key);
        return $val === false ? $default : $val;
    }
}

// Load env file once on bootstrap
app_load_env_file();

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

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function json_response(array $payload, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $message, int $code = 400, array $extra = []): void
{
    json_response(array_merge(['success' => false, 'message' => $message], $extra), $code);
}

function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return $_POST;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $_POST;
}

function pick_default_avatar(string $seed): string
{
    $avatars = DEFAULT_AVATARS;
    $n = abs(crc32($seed));
    return $avatars[$n % count($avatars)];
}

function format_user_row(array $row): array
{
    $avatar = $row['avatar'] ?? null;
    if (!$avatar) {
        $avatar = pick_default_avatar($row['username'] ?? $row['email'] ?? 'user');
    }
    return [
        'id'              => (int) $row['id'],
        'username'        => $row['username'],
        'email'           => $row['email'],
        'avatar'          => $avatar,
        'tier'            => $row['membership_tier'],
        'membership_tier' => $row['membership_tier'],
        'loggedIn'        => true,
    ];
}
