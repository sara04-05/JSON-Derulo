<?php
declare(strict_types=1);

$pageSlug = 'mock-interview';
$pageTitle = 'Mock Interview Coach — ElevUra AI';
$activeNav = 'career-coach';
$extraStylesheets = ['css/tool-shell.css', 'css/career-coach.css'];
$extraScripts = ['MockInterview.js'];

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
?>
            <section class="content-area">
<?php require __DIR__ . '/includes/views/tools/career-coach.php'; ?>
            </section>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
