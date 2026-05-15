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

// Hugging Face Inference Providers API Call
$hfKey = !empty($geminiKey) ? $geminiKey : getenv('HF_API_KEY');

if (empty($hfKey)) {
    json_error('Hugging Face API key is missing. Please provide it in the form.');
}

// Model fallback chain — full repository names, all publicly available on HF Inference
$modelCandidates = [
    'mistralai/Mistral-7B-Instruct-v0.3',
    'HuggingFaceH4/zephyr-7b-beta',
    'Qwen/Qwen2.5-7B-Instruct',
    'mistralai/Mistral-7B-Instruct-v0.2',
    'meta-llama/Llama-3.2-3B-Instruct',
];

// Build the system + user messages (OpenAI chat-completions format)
$systemMessage = "You are an expert career coach and interviewer. You ALWAYS respond with valid JSON only. No markdown, no explanations, no preamble — just a single JSON object.";

$userMessage = "Generate a high-quality interview preparation " . $type . " for the following role:
Role: " . $jobTitle . "
Level: " . $jobLevel . "
Industry: " . $industry . "
Skills/Keywords: " . $skills . "
Context: " . $jobContext . "

Rules:
- For 'quiz': Return exactly 5 MCQs. Each item: {\"question\": \"...\", \"options\": [\"A\",\"B\",\"C\",\"D\"], \"correct_index\": 0}
- For 'flashcard': Return exactly 8 cards. Each item: {\"front\": \"...\", \"back\": \"...\"}
- Output ONLY this JSON structure, nothing else:
{\"type\": \"" . $type . "\", \"job_title\": \"" . $jobTitle . "\", \"items\": [...]}";

/**
 * Attempt inference against each candidate model.
 * Uses the HF Inference Providers router (OpenAI-compatible chat/completions).
 * Retries once on 503 (model loading) after a short delay.
 */
function hf_call(string $apiUrl, array $payload, string $hfKey, int $retries = 1): array
{
    for ($attempt = 0; $attempt <= $retries; $attempt++) {
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
        $err      = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            return ['ok' => false, 'error' => 'CURL error: ' . $err, 'code' => 0];
        }

        // 503 = model is loading — wait and retry once
        if ($httpCode === 503 && $attempt < $retries) {
            $body = json_decode($response, true);
            $wait = (int) ($body['estimated_time'] ?? 20);
            sleep(min($wait, 30));
            continue;
        }

        if ($httpCode === 200) {
            return ['ok' => true, 'body' => $response, 'code' => 200];
        }

        // Any other error — return it so caller can try next model
        $body = json_decode($response, true);
        return [
            'ok'    => false,
            'error' => $body['error'] ?? ($body['message'] ?? 'HTTP ' . $httpCode),
            'code'  => $httpCode,
        ];
    }

    return ['ok' => false, 'error' => 'Exhausted retries', 'code' => 503];
}

$aiContent  = null;
$lastError  = '';
$usedModel  = '';

foreach ($modelCandidates as $model) {
    // HF Inference Providers — OpenAI-compatible chat/completions endpoint
    $apiUrl = "https://router.huggingface.co/hf-inference/models/" . $model . "/v1/chat/completions";

    $payload = [
        "model"    => $model,
        "messages"  => [
            ["role" => "system", "content" => $systemMessage],
            ["role" => "user",   "content" => $userMessage],
        ],
        "max_tokens"  => 2048,
        "temperature" => 0.4,
    ];

    $result = hf_call($apiUrl, $payload, $hfKey);

    if ($result['ok']) {
        $resData = json_decode($result['body'], true);
        $aiContent = $resData['choices'][0]['message']['content'] ?? null;
        if ($aiContent) {
            $usedModel = $model;
            break;
        }
    }

    $lastError = $result['error'] ?? 'No content returned';
}

if (!$aiContent) {
    json_error('All AI models failed. Last error: ' . $lastError);
}

// ── Clean AI output ───────────────────────────────────────────────
$aiContent = trim($aiContent);

// Strip markdown code fences if present
if (strpos($aiContent, '```') !== false) {
    $aiContent = preg_replace('/^```(?:json)?\s*/i', '', $aiContent);
    $aiContent = preg_replace('/\s*```\s*$/', '', $aiContent);
    $aiContent = trim($aiContent);
}

// ── Backend Validation: strict JSON check ─────────────────────────
$decodedContent = json_decode($aiContent, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($decodedContent['items'])) {
    // Fallback: try to extract JSON object from surrounding text
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
