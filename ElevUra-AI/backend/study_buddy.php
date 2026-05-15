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

// API Keys
$geminiKeyFromInput = $input['geminiKey'] ?? '';
$geminiKey = !empty($geminiKeyFromInput) ? $geminiKeyFromInput : getenv('GEMINI_API_KEY');
$hfKey = getenv('HF_API_KEY'); // Will also check $geminiKey if it's not a Gemini key later

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

// ── AI Generation Logic ───────────────────────────────────────────
$aiContent = null;
$lastError = '';

/**
 * Call Google Gemini API
 */
function call_gemini_api(string $prompt, string $key): ?string {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $key;
    $payload = [
        'contents' => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => [
            'temperature' => 0.7,
            'responseMimeType' => 'application/json'
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
    return null;
}

// 1. Try Gemini if key looks like a Gemini key (starts with AIza)
if (strpos($geminiKey, 'AIza') === 0) {
    $aiContent = call_gemini_api($userPrompt, $geminiKey);
    if (!$aiContent) {
        $lastError = 'Gemini API failed or returned empty content.';
    }
}

// 2. Fallback to Hugging Face Router if Gemini didn't work or wasn't used
if (!$aiContent) {
    // If the provided "geminiKey" doesn't look like Gemini, it might be an HF key
    $effectiveHfKey = $hfKey;
    if (empty($effectiveHfKey) && !empty($geminiKeyFromInput) && strpos($geminiKeyFromInput, 'AIza') !== 0) {
        $effectiveHfKey = $geminiKeyFromInput;
    }
    
    if ($effectiveHfKey) {
        $providerModels = [
            ['provider' => 'novita',       'model' => 'meta-llama/Llama-3.1-8B-Instruct'],
            ['provider' => 'together',     'model' => 'meta-llama/Llama-3.3-70B-Instruct'],
            ['provider' => 'fireworks-ai', 'model' => 'meta-llama/Llama-3.3-70B-Instruct'],
            ['provider' => 'cerebras',     'model' => 'meta-llama/Llama-3.1-8B-Instruct'],
        ];

        $errors = [];
        foreach ($providerModels as $combo) {
            $provider = $combo['provider'];
            $model    = $combo['model'];
            $apiUrl   = 'https://router.huggingface.co/' . $provider . '/models/' . $model . '/v1/chat/completions';

            $payload = [
                'model'    => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant. You ONLY respond with valid JSON.'],
                    ['role' => 'user',   'content' => $userPrompt],
                ],
                'max_tokens' => 1024,
            ];

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $effectiveHfKey,
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $resData   = json_decode($response, true);
                $aiContent = $resData['choices'][0]['message']['content'] ?? null;
                if ($aiContent) break;
            } else {
                $body = json_decode($response, true);
                $errors[] = $provider . ': ' . ($body['error'] ?? 'HTTP ' . $httpCode);
            }
        }
        
        if (!$aiContent && !empty($errors)) {
            $lastError = implode(' | ', $errors);
        }
    } else if (empty($lastError)) {
        $lastError = 'No valid API key provided (Gemini or Hugging Face).';
    }
}

if (!$aiContent) {
    json_error('All AI providers failed. Details: ' . $lastError);
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
