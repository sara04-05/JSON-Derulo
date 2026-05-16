<?php
declare(strict_types=1);

$pageSlug = 'cv-optimizer';
$pageTitle = 'Resume Rater — ElevUra AI';
$activeNav = 'cv-optimizer';
$extraStylesheets = ['css/tool-shell.css', 'resume-rater.css', 'css/cv-optimizer.css'];
$extraScripts = [
    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js',
    'resume-rater-extract.js',
    'resume-rater-analyzer.js',
    'resume-rater-ui.js',
    'resume-rater.js',
];
$extraScriptBlocks = [
    '<script>if (window.pdfjsLib) { pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"; }</script>',
    '<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>',
];

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/layout-start.php';
?>
            <section class="content-area">
<?php require __DIR__ . '/includes/views/tools/cv-optimizer.php'; ?>
            </section>
<?php require __DIR__ . '/includes/layout-end.php'; ?>
