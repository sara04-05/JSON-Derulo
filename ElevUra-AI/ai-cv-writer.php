<?php
declare(strict_types=1);

$pageSlug = 'ai-cv-writer';
$pageTitle = 'AI CV Writer — ElevUra AI';
$activeNav = 'ai-cv-writer';
$extraStylesheets = ['css/tool-shell.css', 'CVwriter.css'];
$extraScripts = [
    'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js',
    'CVwriter.js',
];

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
?>
            <section class="content-area">
<?php require __DIR__ . '/includes/views/tools/ai-cv-writer.php'; ?>
            </section>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
