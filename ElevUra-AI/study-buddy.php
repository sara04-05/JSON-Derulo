<?php
declare(strict_types=1);

$pageSlug = 'study-buddy';
$pageTitle = 'Study Buddy — ElevUra';
$activeNav = 'study-buddy';

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
?>
            <section class="content-area">
<?php require __DIR__ . '/includes/views/study-buddy.php'; ?>
            </section>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
