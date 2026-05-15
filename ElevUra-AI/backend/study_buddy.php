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

// Model: confirmed "warm" on HF Inference (verified via HF Hub API)
$model  = 'mistralai/Mistral-7B-Instruct-v0.2';

// Current working endpoint — the old api-inference.huggingface.co is deprecated
$apiUrl = 'https://router.huggingface.co/hf-inference/models/' . $model . '/v1/chat/completions';

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

$payload = [
    'model'       => $model,
    'messages'    => [
        ['role' => 'system', 'content' => 'You are a helpful assistant. You ONLY respond with valid JSON. No markdown, no explanations.'],
        ['role' => 'user',   'content' => $userPrompt],
    ],
    'max_tokens'  => 1024,
    'temperature' => 0.7,
];

// Send request (with one retry for 503 / model loading)
$aiContent = null;
$maxRetries = 1;

for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
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
        json_error('AI Service Error: ' . $curlErr);
    }

    // 503 = model is cold-starting — wait then retry once
    if ($httpCode === 503 && $attempt < $maxRetries) {
        $body = json_decode($response, true);
        $wait = (int) ($body['estimated_time'] ?? 20);
        sleep(min($wait, 30));
        continue;
    }

    if ($httpCode !== 200) {
        $body = json_decode($response, true);
        $msg  = $body['error'] ?? ($body['message'] ?? 'HTTP ' . $httpCode);
        json_error('Hugging Face API Error (' . $httpCode . '): ' . $msg);
    }

    // Parse OpenAI-compatible chat response
    $resData = json_decode($response, true);
    $aiContent = $resData['choices'][0]['message']['content'] ?? null;

    if ($aiContent) {
        break;
    }
}

if (!$aiContent) {
    json_error('AI returned empty content. Response: ' . substr($response ?? '', 0, 200));
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
