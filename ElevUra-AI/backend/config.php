<?php
/**
 * ElevUra — database configuration (XAMPP defaults)
 * Adjust credentials if your MySQL setup differs.
 */
declare(strict_types=1);

if (!function_exists('app_load_env_file')) {
    function app_load_env_file(?string $path = null): void
    {
        static $loaded = [];
        $path = $path ?? __DIR__ . '/../.env';
        if (isset($loaded[$path])) {
            return;
        }
        $loaded[$path] = true;

        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            $separator = strpos($line, '=');
            if ($separator === false) {
                continue;
            }

            $name = trim(substr($line, 0, $separator));
            if ($name === '') {
                continue;
            }

            $value = trim(substr($line, $separator + 1));
            $valueLength = strlen($value);
            if ($valueLength >= 2) {
                $first = $value[0];
                $last = $value[$valueLength - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            $value = str_replace(['\\n', '\\r', '\\t'], ["\n", "\r", "\t"], $value);
            putenv($name . '=' . $value);
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

if (!function_exists('app_env')) {
    function app_env(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }

        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }

        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }

        return $default;
    }
}

app_load_env_file(__DIR__ . '/../.env');

define('DB_HOST', 'localhost');
define('DB_NAME', 'elevura');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/** Default avatar pool when user has no custom avatar */
define('DEFAULT_AVATARS', [
    'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=128&h=128&fit=crop&crop=faces',
    'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=128&h=128&fit=crop&crop=faces',
    'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=128&h=128&fit=crop&crop=faces',
]);
