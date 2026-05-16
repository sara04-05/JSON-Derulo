<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'ElevUra') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="<?= $loggedIn ? 'is-logged-in tools-unlocked' : 'is-logged-out' ?>" data-page="<?= e($pageSlug ?? 'home') ?>">
