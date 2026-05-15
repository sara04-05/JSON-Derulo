<?php
/**
 * ElevUra — database configuration (XAMPP defaults)
 * Adjust credentials if your MySQL setup differs.
 */
declare(strict_types=1);

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
