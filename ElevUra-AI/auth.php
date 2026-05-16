<?php
declare(strict_types=1);

$mode = isset($_GET['auth']) && $_GET['auth'] === 'signup' ? 'signup' : 'login';
header('Location: index.php?auth=' . urlencode($mode), true, 302);
exit;
