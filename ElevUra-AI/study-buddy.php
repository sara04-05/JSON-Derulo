<?php
declare(strict_types=1);

$pageSlug = 'study-buddy';
$pageTitle = 'Career Prep — ElevUra';
$activeNav = 'study-buddy';
$extraStylesheets = ['css/study-buddy.css'];

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
?>
            <section class="content-area">
<?php require __DIR__ . '/includes/views/study-buddy.php'; ?>
            </section>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
