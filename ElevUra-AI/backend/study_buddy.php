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



// Hugging Face API Call
$hfKey = !empty($geminiKey) ? $geminiKey : getenv('HF_API_KEY');

if (empty($hfKey)) {
    json_error('Hugging Face API key is missing. Please provide it in the form.');
}

// Stable Instruct Model
$model = "mistralai/Mistral-7B-Instruct-v0.2";
$apiUrl = "https://api-inference.huggingface.co/models/" . $model;

/**
 * Prompt optimization for Mistral Instruct:
 * We use the [INST] tokens to guide the model.
 */
$prompt = "<s>[INST] You are an expert career coach and interviewer. 
Generate a high-quality interview preparation " . $type . " for the following role:
Role: " . $jobTitle . "
Level: " . $jobLevel . "
Industry: " . $industry . "
Skills/Keywords: " . $skills . "

Instructions:
- For 'quiz': Generate 5 multiple-choice questions. Each should have 'question', 'options' (array of 4), and 'correct_index' (0-3).
- For 'flashcard': Generate 8 question-answer pairs. Each should have 'front' and 'back'.
- Output MUST be ONLY valid JSON. No markdown, no preamble.

JSON Schema:
{
  \"type\": \"" . $type . "\",
  \"job_title\": \"" . $jobTitle . "\",
  \"items\": [...]
} [/INST]";

$payload = [
    "inputs" => $prompt,
    "parameters" => [
        "max_new_tokens" => 2048,
        "temperature" => 0.4,
        "return_full_text" => false
    ],
    "options" => [
        "wait_for_model" => true
    ]
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $hfKey
]);

$response = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    json_error('AI Service Error (CURL): ' . $err);
}

if ($httpCode !== 200) {
    $errorData = json_decode($response, true);
    $errorMsg = $errorData['error'] ?? 'Unknown Hugging Face error';
    json_error('Hugging Face API Error (' . $httpCode . '): ' . $errorMsg);
}

$resData = json_decode($response, true);
$aiContent = $resData[0]['generated_text'] ?? null;

if (!$aiContent) {
    json_error('Failed to generate content from AI. Response: ' . $response);
}

// Clean AI output (remove potential markdown wrappers)
$aiContent = trim($aiContent);
if (strpos($aiContent, '```') !== false) {
    $aiContent = preg_replace('/^```(?:json)?\n?/i', '', $aiContent);
    $aiContent = preg_replace('/\n?```$/', '', $aiContent);
    $aiContent = trim($aiContent);
}

// Backend Validation: Ensure valid JSON
$decodedContent = json_decode($aiContent, true);
if (json_last_error() !== JSON_ERROR_NONE || !isset($decodedContent['items'])) {
    // If it fails, try one more time to find JSON within the text
    $jsonStart = strpos($aiContent, '{');
    $jsonEnd = strrpos($aiContent, '}');
    if ($jsonStart !== false && $jsonEnd !== false) {
        $aiContent = substr($aiContent, $jsonStart, $jsonEnd - $jsonStart + 1);
        $decodedContent = json_decode($aiContent, true);
    }
    
    if (json_last_error() !== JSON_ERROR_NONE || !isset($decodedContent['items'])) {
        json_error('AI returned invalid format. Please try again. Raw: ' . substr($aiContent, 0, 100) . '...');
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
