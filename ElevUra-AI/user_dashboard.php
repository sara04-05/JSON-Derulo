<?php
declare(strict_types=1);

$pageSlug = 'user-dashboard';
$pageTitle = 'Mission Control — ElevUra';
$activeNav = 'dashboard';

require_once __DIR__ . '/includes/init.php';

if (!$loggedIn && $authPrompt === '') {
    $authPrompt = 'login';
}

require_once __DIR__ . '/includes/layout-start.php';
require __DIR__ . '/includes/views/user-dashboard.php';
require_once __DIR__ . '/includes/layout-end.php';
