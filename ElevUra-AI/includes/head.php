<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'ElevUra') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
<?php foreach ($extraStylesheets ?? [] as $stylesheet): ?>
    <link rel="stylesheet" href="<?= e($stylesheet) ?>">
<?php endforeach; ?>
<?php if (!empty($inlineStyles)): ?>
    <style><?= $inlineStyles ?></style>
<?php endif; ?>
</head>
<body class="<?= $loggedIn ? 'is-logged-in tools-unlocked' : 'is-logged-out' ?>" data-page="<?= e($pageSlug ?? 'home') ?>">
