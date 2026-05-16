<?php
declare(strict_types=1);
$pageSlug = 'study-buddy';
$pageTitle = 'Study Buddy — ElevUra';
$activeNav = '';
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/head.php';
?>
    <div class="container-wrapper">
<?php require __DIR__ . '/includes/sidebar.php'; ?>
        <main class="main-content">
<?php require __DIR__ . '/includes/header.php'; ?>
<?php require __DIR__ . '/includes/views/study-buddy.php'; ?>
        </main>
    </div>
<?php require __DIR__ . '/includes/auth-modal.php'; ?>
<?php require __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
