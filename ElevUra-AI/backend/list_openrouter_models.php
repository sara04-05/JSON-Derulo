<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$apiKey = trim((string) app_env('OPENROUTER_API_KEY', ''));
if ($apiKey === '') {
    echo json_encode(['ok' => false, 'error' => 'OPENROUTER_API_KEY missing from environment (.env)']);
    exit(1);
}

$endpoint = 'https://openrouter.ai/api/v1/models';

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Accept: application/json',
    'HTTP-Referer: http://localhost',
    'X-Title: ElevUra AI',
]);

$resp = curl_exec($ch);
$err = curl_error($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$ok = ($err === '' && $resp !== false && ($info['http_code'] ?? 0) >= 200 && ($info['http_code'] ?? 0) < 300);

$out = [
    'ok' => $ok,
    'http_code' => $info['http_code'] ?? null,
    'curl_error' => $err ?: null,
    'response_raw' => $resp,
];

// Log for later inspection
file_put_contents(__DIR__ . '/openrouter_models.log', date('c') . "\n" . ($resp ?: $err) . "\n\n", FILE_APPEND | LOCK_EX);

echo json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

exit(0);
