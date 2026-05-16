<?php
declare(strict_types=1);

$pageSlug = 'career-path';
$pageTitle = 'Career Prep — ElevUra';
$activeNav = 'career-path';
$extraStylesheets = ['css/career-path.css'];

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
?>
            <section class="content-area">
<?php require __DIR__ . '/includes/views/career-path.php'; ?>
            </section>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
