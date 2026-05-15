<?php
/**
 * ElevUra — Study Buddy AI Generation Backend
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

// Ensure user is logged in
$userId = require_auth();

// Read input
$input = read_json_body();
$type = $input['type'] ?? 'quiz'; // quiz, flashcard, explanation, roadmap
$jobTitle = $input['jobTitle'] ?? '';
$jobLevel = $input['jobLevel'] ?? '';
$jobContext = $input['jobContext'] ?? '';
$industry = $input['industry'] ?? '';
$skills = $input['skills'] ?? '';
$geminiKey = $input['geminiKey'] ?? ''; // API Key can be passed from frontend

if (empty($jobTitle)) {
    json_error('Job title is required to generate study materials.');
}

// Hugging Face Inference API Call
$hfKey = !empty($geminiKey) ? $geminiKey : getenv('HF_API_KEY');

if (empty($hfKey)) {
    json_error('Hugging Face API key is missing. Please provide it in the form.');
}

// Provider + model combos confirmed available via HF Hub API.
// The HF router proxies to these providers using your HF token.
// Format: router.huggingface.co/{provider}/models/{model}/v1/chat/completions
$providerModels = [
    ['provider' => 'novita',       'model' => 'meta-llama/Llama-3.1-8B-Instruct'],
    ['provider' => 'cerebras',     'model' => 'meta-llama/Llama-3.1-8B-Instruct'],
    ['provider' => 'together',     'model' => 'meta-llama/Llama-3.3-70B-Instruct'],
    ['provider' => 'fireworks-ai', 'model' => 'meta-llama/Llama-3.3-70B-Instruct'],
    ['provider' => 'novita',       'model' => 'meta-llama/Meta-Llama-3-8B-Instruct'],
    ['provider' => 'cohere',       'model' => 'CohereLabs/c4ai-command-r7b-12-2024'],
];

// Build prompt for the requested type
if ($type === 'flashcard') {
    $userPrompt = "Generate 8 interview flashcards for a " . $jobLevel . " " . $jobTitle . " role in " . $industry . "."
        . " Skills: " . $skills . "."
        . " Return ONLY valid JSON with no extra text: {\"type\": \"flashcard\", \"job_title\": \"" . $jobTitle . "\", \"items\": [{\"front\": \"question\", \"back\": \"answer\"}, ...]}";
} else {
    $userPrompt = "Generate 5 multiple-choice interview questions for a " . $jobLevel . " " . $jobTitle . " role in " . $industry . "."
        . " Skills: " . $skills . "."
        . " Return ONLY valid JSON with no extra text: {\"type\": \"quiz\", \"job_title\": \"" . $jobTitle . "\", \"items\": [{\"question\": \"text\", \"options\": [\"A\",\"B\",\"C\",\"D\"], \"correct_index\": 0}, ...]}";
}

// Try each provider+model combo until one succeeds
$aiContent  = null;
$lastError  = '';

foreach ($providerModels as $combo) {
    $provider = $combo['provider'];
    $model    = $combo['model'];

    $apiUrl = 'https://router.huggingface.co/' . $provider . '/models/' . $model . '/v1/chat/completions';

    $payload = [
        'model'       => $model,
        'messages'    => [
            ['role' => 'system', 'content' => 'You are a helpful assistant. You ONLY respond with valid JSON. No markdown, no explanations.'],
            ['role' => 'user',   'content' => $userPrompt],
        ],
        'max_tokens'  => 1024,
        'temperature' => 0.7,
    ];

    // Attempt with one retry for 503 (cold start)
    for ($attempt = 0; $attempt <= 1; $attempt++) {
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $hfKey,
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);

        $response = curl_exec($ch);
        $curlErr  = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($curlErr) {
            $lastError = 'CURL: ' . $curlErr;
            break; // try next provider
        }

        if ($httpCode === 503 && $attempt === 0) {
            $body = json_decode($response, true);
            $wait = (int) ($body['estimated_time'] ?? 15);
            sleep(min($wait, 20));
            continue;
        }

        if ($httpCode === 200) {
            $resData   = json_decode($response, true);
            $aiContent = $resData['choices'][0]['message']['content'] ?? null;
            if ($aiContent) break 2; // success — exit both loops
        }

        // Non-200: log and try next provider
        $body = json_decode($response, true);
        $lastError = ($provider . '/' . $model . ': '
            . ($body['error'] ?? ($body['message'] ?? 'HTTP ' . $httpCode)));
        break; // try next provider
    }
}

if (!$aiContent) {
    json_error('All AI providers failed. Last error: ' . $lastError);
}

// ── Clean AI output ───────────────────────────────────────────────
$aiContent = trim($aiContent);
if (strpos($aiContent, '```') !== false) {
    $aiContent = preg_replace('/^```(?:json)?\s*/i', '', $aiContent);
    $aiContent = preg_replace('/\s*```\s*$/', '', $aiContent);
    $aiContent = trim($aiContent);
}

// ── Backend Validation ────────────────────────────────────────────
$decodedContent = json_decode($aiContent, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($decodedContent['items'])) {
    // Try to extract JSON from surrounding text
    $jsonStart = strpos($aiContent, '{');
    $jsonEnd   = strrpos($aiContent, '}');
    if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
        $aiContent = substr($aiContent, $jsonStart, $jsonEnd - $jsonStart + 1);
        $decodedContent = json_decode($aiContent, true);
    }

    if (json_last_error() !== JSON_ERROR_NONE || !isset($decodedContent['items'])) {
        json_error('AI returned invalid JSON. Please try again.');
    }
}

// Save to Database
try {
    $pdo = get_db();
    $stmt = $pdo->prepare(
        'INSERT INTO study_materials (user_id, material_type, job_title, job_level, content_json) 
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $type,
        $jobTitle,
        $jobLevel,
        $aiContent
    ]);
    
    json_response([
        'success' => true,
        'material_id' => $pdo->lastInsertId(),
        'data' => $decodedContent
    ]);
} catch (PDOException $e) {
    json_error('Database error while saving study material: ' . $e->getMessage());
}
