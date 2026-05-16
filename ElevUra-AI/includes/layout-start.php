<?php
declare(strict_types=1);
/**
 * App shell open: head, sidebar, main, header.
 * Expects init.php loaded; optional $pageTitle, $pageSlug, $activeNav, $extraStylesheets, $inlineStyles.
 */
require_once __DIR__ . '/head.php';
?>
    <div class="container-wrapper">
<?php require __DIR__ . '/sidebar.php'; ?>
        <main class="main-content">
<?php require __DIR__ . '/header.php'; ?>
