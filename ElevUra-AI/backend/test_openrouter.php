<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$apiKey = trim((string) app_env('OPENROUTER_API_KEY', ''));
if ($apiKey === '') {
    echo json_encode(['ok' => false, 'error' => 'OPENROUTER_API_KEY missing from environment (.env)']);
    exit(1);
}


$endpoint = 'https://openrouter.ai/api/v1/chat/completions';
$models = [
    'deepseek/deepseek-chat-v3-0324:free',
    'meta-llama/llama-3.1-8b-instruct:free',
    'mistralai/mistral-7b-instruct:free',
];

$results = [];
$logLines = [];

foreach ($models as $model) {
    $body = [
        'model' => $model,
        'messages' => [
            ['role' => 'system', 'content' => 'Diagnostic test: reply with short OK'],
            ['role' => 'user', 'content' => 'Ping']
        ],
        'max_tokens' => 10,
        'temperature' => 0.0,
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'HTTP-Referer: http://localhost',
        'X-Title: ElevUra AI',
    ]);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    $ok = ($err === '' && $resp !== false && ($info['http_code'] ?? 0) >= 200 && ($info['http_code'] ?? 0) < 300);

    $results[] = [
        'model' => $model,
        'ok' => $ok,
        'http_code' => $info['http_code'] ?? null,
        'curl_error' => $err ?: null,
        'response_raw' => $resp,
    ];

    $logLines[] = sprintf("[%s] model=%s ok=%s http=%s err=%s", date('c'), $model, $ok ? '1' : '0', $info['http_code'] ?? '0', $err ?: '-');
    $logLines[] = "RAW_RESPONSE: " . ($resp === false ? '<<no response>>' : $resp);
    $logLines[] = str_repeat('-', 80);
}

$logPath = __DIR__ . '/openrouter_diagnostics.log';
file_put_contents($logPath, implode("\n", $logLines) . "\n", FILE_APPEND | LOCK_EX);

$out = [
    'ok' => true,
    'endpoint' => $endpoint,
    'results' => $results,
    'log_file' => 'backend/openrouter_diagnostics.log',
];

echo json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

exit(0);
