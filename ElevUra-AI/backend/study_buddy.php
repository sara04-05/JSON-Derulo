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

// Guaranteed supported model on HF serverless inference
$model  = 'google/flan-t5-large';
$apiUrl = 'https://api-inference.huggingface.co/models/' . $model;

// Build a direct instruction prompt (no chat roles)
if ($type === 'flashcard') {
    $prompt = "Generate 8 interview flashcards for a " . $jobLevel . " " . $jobTitle . " role in " . $industry . "."
        . " Skills: " . $skills . "."
        . " Return ONLY valid JSON: {\"type\": \"flashcard\", \"job_title\": \"" . $jobTitle . "\", \"items\": [{\"front\": \"question\", \"back\": \"answer\"}, ...]}";
} else {
    $prompt = "Generate 5 multiple-choice interview questions for a " . $jobLevel . " " . $jobTitle . " role in " . $industry . "."
        . " Skills: " . $skills . "."
        . " Return ONLY valid JSON: {\"type\": \"quiz\", \"job_title\": \"" . $jobTitle . "\", \"items\": [{\"question\": \"text\", \"options\": [\"A\",\"B\",\"C\",\"D\"], \"correct_index\": 0}, ...]}";
}

$payload = [
    'inputs'     => $prompt,
    'parameters' => [
        'max_new_tokens' => 500,
        'temperature'    => 0.7,
    ],
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
        $msg  = $body['error'] ?? ('HTTP ' . $httpCode);
        json_error('Hugging Face API Error: ' . $msg);
    }

    // Parse successful response
    $resData = json_decode($response, true);

    // HF text-generation returns an array: [{"generated_text": "..."}]
    if (isset($resData[0]['generated_text'])) {
        $aiContent = $resData[0]['generated_text'];
    }
    // text2text-generation (flan-t5) may also return this shape
    elseif (isset($resData['generated_text'])) {
        $aiContent = $resData['generated_text'];
    }

    if ($aiContent) {
        break;
    }
}

if (!$aiContent) {
    json_error('AI returned empty content. Response: ' . substr($response, 0, 200));
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
