<?php
declare(strict_types=1);

$pageSlug = 'home';
$pageTitle = 'ElevUra Dashboard - AI Command Center';
$activeNav = 'home';

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
require __DIR__ . '/includes/views/command-center.php';
require_once __DIR__ . '/includes/layout-end.php';
