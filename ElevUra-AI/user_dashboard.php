<?php
declare(strict_types=1);
$pageSlug = 'user-dashboard';
$pageTitle = 'Mission Control — ElevUra';
$activeNav = 'dashboard';
require_once __DIR__ . '/includes/init.php';

if (!$loggedIn && $authPrompt === '') {
    $authPrompt = 'login';
}

require_once __DIR__ . '/includes/head.php';
?>
    <div class="container-wrapper">
<?php require __DIR__ . '/includes/sidebar.php'; ?>
        <main class="main-content">
<?php require __DIR__ . '/includes/header.php'; ?>
<?php require __DIR__ . '/includes/views/user-dashboard.php'; ?>
        </main>
    </div>
<?php require __DIR__ . '/includes/auth-modal.php'; ?>
<?php require __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
