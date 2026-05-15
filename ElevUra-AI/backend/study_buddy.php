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

// Prepare AI Prompt
$prompt = "You are an expert career coach and interviewer. 
Generate a high-quality interview preparation " . $type . " for the following role:
Role: " . $jobTitle . "
Level: " . $jobLevel . "
Industry: " . $industry . "
Skills/Keywords: " . $skills . "
Context/Job Description: " . $jobContext . "

Instructions:
- For 'quiz': Generate 5 multiple-choice questions. Each should have 'question', 'options' (array of 4), and 'correct_index'.
- For 'flashcard': Generate 8 question-answer pairs. Each should have 'front' and 'back'.
- Output MUST be valid JSON only. Do not include any markdown or extra text.

Format:
{
  \"type\": \"" . $type . "\",
  \"job_title\": \"" . $jobTitle . "\",
  \"items\": [...]
}";

// Gemini API Call
$apiKey = !empty($geminiKey) ? $geminiKey : getenv('GEMINI_API_KEY');

if (empty($apiKey)) {
    json_error('Gemini API key is missing. Please provide it in the form.');
}

$apiUrl = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=" . $apiKey;

$payload = [
    "contents" => [
        [
            "parts" => [
                ["text" => $prompt]
            ]
        ]
    ],
    "generationConfig" => [
        "response_mime_type" => "application/json"
    ]
];

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    json_error('AI Service Error: ' . $err);
}

$resData = json_decode($response, true);
$aiContent = $resData['candidates'][0]['content']['parts'][0]['text'] ?? null;

if (!$aiContent) {
    json_error('Failed to generate content from AI. Response: ' . $response);
}

// Clean AI output if necessary (though response_mime_type should handle it)
$decodedContent = json_decode($aiContent, true);
if (!$decodedContent) {
    json_error('AI returned invalid JSON format.');
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
