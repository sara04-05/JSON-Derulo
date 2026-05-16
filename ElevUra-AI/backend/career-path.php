<?php
/**
 * ElevUra — Career Prep AI Generation Backend
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

const STUDY_BUDDY_OPENROUTER_MODELS = [
    'deepseek/deepseek-v4-flash:free',
    'openai/gpt-oss-20b:free',
    'meta-llama/llama-3.2-3b-instruct:free',
    'qwen/qwen3-next-80b-a3b-instruct:free',
];

const STUDY_BUDDY_OPENROUTER_ENDPOINT = 'https://openrouter.ai/api/v1/chat/completions';

$userId = require_auth();
$input = read_json_body();

$requestedType = (string) ($input['type'] ?? 'quiz');
$type = $requestedType === 'flashcard' ? 'flashcard' : 'quiz';
$jobTitle = trim((string) ($input['jobTitle'] ?? ''));
$jobLevel = trim((string) ($input['jobLevel'] ?? ''));
$jobContext = trim((string) ($input['jobContext'] ?? ''));
$industry = trim((string) ($input['industry'] ?? ''));
$skills = trim((string) ($input['skills'] ?? ''));
$openRouterToken = trim((string) app_env('OPENROUTER_API_KEY', ''));

if ($jobTitle === '') {
    json_error('Job title is required to generate study materials.');
}

function study_buddy_build_prompt(string $type, string $jobTitle, string $jobLevel, string $industry, string $skills, string $jobContext): string
{
    $roleParts = array_filter([$jobLevel, $jobTitle, $industry], static fn(string $value): bool => $value !== '');
    $roleLabel = implode(' ', $roleParts);
    $context = $jobContext !== '' ? "\nJob context: {$jobContext}" : '';
    $skillLine = $skills !== '' ? "\nSkills to emphasize: {$skills}" : '';

    if ($type === 'flashcard') {
        return 'Create exactly 8 interview flashcards for the following role.'
            . "\nRole: {$roleLabel}"
            . $skillLine
            . $context
            . "\nReturn only valid JSON in this exact shape: {\"flashcards\":[{\"question\":\"...\",\"answer\":\"...\"}]}"
            . "\nUse exactly 8 cards. Do not add markdown, code fences, or commentary.";
    }

    return 'Create exactly 5 multiple-choice interview questions for the following role.'
        . "\nRole: {$roleLabel}"
        . $skillLine
        . $context
        . "\nReturn only valid JSON in this exact shape: {\"quiz\":[{\"question\":\"...\",\"options\":[\"...\",\"...\",\"...\",\"...\"],\"answer\":\"A\"}]}"
        . "\nUse exactly 5 questions and exactly 4 options per question. The answer must be A, B, C, or D. Do not add markdown, code fences, or commentary.";
}

function study_buddy_encode_model_id(string $model): string
{
    return str_replace('%2F', '/', rawurlencode($model));
}

function study_buddy_call_openrouter(string $prompt, string $token, string $model): array
{
    $payload = [
        'model' => $model,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'Return only valid JSON for quizzes or flashcards. Do not add markdown, code fences, or commentary.',
            ],
            [
                'role' => 'user',
                'content' => $prompt,
            ],
        ],
        'max_tokens' => 700,
        'temperature' => 0.2,
    ];

    $ch = curl_init(STUDY_BUDDY_OPENROUTER_ENDPOINT);
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Expect:',
        'Authorization: Bearer ' . $token,
        'HTTP-Referer: ' . (string) (app_env('OPENROUTER_HTTP_REFERER', '') ?: 'http://localhost'),
        'X-Title: ElevUra Career Prep',
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'curl/8.0.0',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_errno($ch) ? curl_error($ch) : '';
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false) {
        return [
            'ok' => false,
            'error' => $curlError !== '' ? $curlError : 'The OpenRouter request could not be completed.',
            'http_code' => $httpCode,
        ];
    }

    $decoded = json_decode($response, true);
    if ($httpCode !== 200) {
        $message = 'OpenRouter returned HTTP ' . $httpCode . ' for model ' . $model . '.';
        if (is_array($decoded) && isset($decoded['error'])) {
            $message = study_buddy_error_to_string($decoded['error']);
        }

        return [
            'ok' => false,
            'error' => $message,
            'http_code' => $httpCode,
            'raw' => $response,
        ];
    }

    $generatedText = null;
    if (is_array($decoded)) {
        if (isset($decoded['choices'][0]['message']['content'])) {
            $generatedText = $decoded['choices'][0]['message']['content'];
        } elseif (isset($decoded['error'])) {
            return [
                'ok' => false,
                'error' => study_buddy_error_to_string($decoded['error']),
                'http_code' => $httpCode,
                'raw' => $response,
            ];
        }
    }

    if (!is_string($generatedText) || trim($generatedText) === '') {
        return [
            'ok' => false,
            'error' => 'The free OpenRouter model returned an empty response.',
            'http_code' => $httpCode,
            'raw' => $response,
        ];
    }

    return [
        'ok' => true,
        'content' => $generatedText,
        'http_code' => $httpCode,
    ];
}

function study_buddy_parse_model_json(string $text): ?array
{
    $decoded = json_decode($text, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        return $decoded;
    }

    $jsonFragment = study_buddy_extract_json($text);
    if ($jsonFragment === null) {
        return null;
    }

    $decoded = json_decode($jsonFragment, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        return null;
    }

    return $decoded;
}

function study_buddy_extract_json(string $text): ?string
{
    $text = trim($text);
    $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
    $text = preg_replace('/\s*```\s*$/', '', $text);
    $text = trim($text);

    $jsonStart = strpos($text, '{');
    $jsonEnd = strrpos($text, '}');
    if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
        return substr($text, $jsonStart, $jsonEnd - $jsonStart + 1);
    }

    $arrayStart = strpos($text, '[');
    $arrayEnd = strrpos($text, ']');
    if ($arrayStart !== false && $arrayEnd !== false && $arrayEnd > $arrayStart) {
        return substr($text, $arrayStart, $arrayEnd - $arrayStart + 1);
    }

    return null;
}

function study_buddy_normalize_payload(array $decoded, string $type, string $jobTitle): ?array
{
    $itemsSource = $decoded['items'] ?? $decoded['quiz'] ?? $decoded['flashcards'] ?? $decoded['questions'] ?? $decoded['cards'] ?? null;
    if ($itemsSource === null && study_buddy_is_list($decoded)) {
        $itemsSource = $decoded;
    }

    if (!is_array($itemsSource)) {
        return null;
    }

    $items = [];

    if ($type === 'flashcard') {
        foreach ($itemsSource as $item) {
            if (!is_array($item)) {
                continue;
            }

            $front = trim((string) ($item['front'] ?? $item['question'] ?? $item['prompt'] ?? ''));
            $back = trim((string) ($item['back'] ?? $item['answer'] ?? $item['explanation'] ?? ''));
            if ($front === '' || $back === '') {
                continue;
            }

            $items[] = [
                'front' => $front,
                'back' => $back,
            ];
        }
    } else {
        foreach ($itemsSource as $item) {
            if (!is_array($item)) {
                continue;
            }

            $question = trim((string) ($item['question'] ?? $item['prompt'] ?? ''));
            $options = $item['options'] ?? $item['choices'] ?? [];
            $correctIndex = study_buddy_resolve_correct_index($item, $options);

            if ($question === '' || !is_array($options)) {
                continue;
            }

            $cleanOptions = [];
            foreach ($options as $option) {
                $optionText = trim((string) $option);
                if ($optionText !== '') {
                    $cleanOptions[] = $optionText;
                }
            }

            if (count($cleanOptions) < 2) {
                continue;
            }

            if ($correctIndex < 0 || $correctIndex >= count($cleanOptions)) {
                $correctIndex = 0;
            }

            $items[] = [
                'question' => $question,
                'options' => array_values($cleanOptions),
                'correct_index' => $correctIndex,
            ];
        }
    }

    if (!$items) {
        return null;
    }

    return [
        'type' => $type,
        'job_title' => (string) ($decoded['job_title'] ?? $jobTitle),
        'items' => $items,
    ];
}

function study_buddy_resolve_correct_index(array $item, array $options): int
{
    $correctIndex = $item['correct_index'] ?? $item['answer_index'] ?? null;
    if (is_int($correctIndex) || (is_string($correctIndex) && is_numeric($correctIndex))) {
        return (int) $correctIndex;
    }

    $answer = $item['answer'] ?? $item['correct_answer'] ?? $item['correct'] ?? null;
    if (!is_string($answer)) {
        return 0;
    }

    $answer = trim($answer);
    if ($answer === '') {
        return 0;
    }

    $letterIndex = strtoupper($answer[0] ?? '');
    $letterMap = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];
    if (isset($letterMap[$letterIndex])) {
        return $letterMap[$letterIndex];
    }

    foreach ($options as $index => $option) {
        if (is_string($option) && strcasecmp(trim($option), $answer) === 0) {
            return (int) $index;
        }
    }

    return 0;
}

function study_buddy_is_list(array $values): bool
{
    return $values === [] || array_keys($values) === range(0, count($values) - 1);
}

function study_buddy_openrouter_auth_failed(array $errors): bool
{
    foreach ($errors as $error) {
        $message = strtolower((string) $error);
        if (str_contains($message, 'user not found') || str_contains($message, '"code":401') || str_contains($message, 'http 401')) {
            return true;
        }
    }

    return false;
}

function study_buddy_build_fallback_warning(array $errors): string
{
    if (study_buddy_openrouter_auth_failed($errors)) {
        return 'AI generation failed because your OpenRouter API key is invalid or expired. '
            . 'Create a new key at openrouter.ai/keys and set OPENROUTER_API_KEY in ElevUra-AI/.env, then restart Apache. '
            . 'Showing offline practice content for now.';
    }

    if ($errors !== []) {
        return 'AI generation is temporarily unavailable. Showing offline practice content for now.';
    }

    return '';
}

function study_buddy_error_to_string(mixed $error): string
{
    if (is_string($error)) {
        return $error;
    }

    if (is_int($error) || is_float($error) || is_bool($error) || $error === null) {
        return (string) $error;
    }

    $encoded = json_encode($error, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($encoded !== false) {
        return $encoded;
    }

    return 'Unknown Hugging Face error.';
}

function study_buddy_split_skills(string $skills): array
{
    $skills = trim($skills);
    if ($skills === '') {
        return [];
    }

    $parts = preg_split('/[,;|\/]+/u', $skills) ?: [];
    return array_values(array_filter(array_map(static function (string $part): string {
        return trim($part);
    }, $parts), static fn(string $part): bool => $part !== ''));
}

function study_buddy_build_local_payload(string $type, string $jobTitle, string $jobLevel, string $industry, string $skills, string $jobContext): array
{
    $skillList = study_buddy_split_skills($skills);
    $focusSkill = $skillList[0] ?? ($jobTitle !== '' ? $jobTitle : 'core role skills');
    $secondarySkill = $skillList[1] ?? ($industry !== '' ? $industry : 'communication');
    $thirdSkill = $skillList[2] ?? ($jobLevel !== '' ? $jobLevel : 'problem solving');

    if ($type === 'flashcard') {
        return [
            'type' => 'flashcard',
            'job_title' => $jobTitle,
            'items' => [
                ['front' => "What is the core responsibility of a {$jobTitle}?", 'back' => $jobContext !== '' ? $jobContext : 'Explain the role clearly and connect it to measurable outcomes.'],
                ['front' => "Which skill should a {$jobTitle} demonstrate first?", 'back' => $focusSkill],
                ['front' => 'How do you communicate progress on a complex task?', 'back' => 'Share status, blockers, and next steps with concise updates.'],
                ['front' => 'What helps you stay effective under pressure?', 'back' => 'Prioritization, clear milestones, and calm execution.'],
                ['front' => "How does {$industry} change the way you approach this role?", 'back' => $industry !== '' ? "Tailor your answer to the realities of {$industry}." : 'Tailor your answer to the target company and team.'],
                ['front' => 'What is one way to improve results fast?', 'back' => "Strengthen {$secondarySkill} and review feedback regularly."],
                ['front' => 'How do you handle an unfamiliar challenge?', 'back' => 'Break it down, verify assumptions, and ask precise questions.'],
                ['front' => 'What shows strong growth potential?', 'back' => "Consistent learning, reflection, and ownership of {$thirdSkill}."],
            ],
        ];
    }

    return [
        'type' => 'quiz',
        'job_title' => $jobTitle,
        'items' => [
            ['question' => "Which skill should a {$jobTitle} demonstrate most strongly?", 'options' => [$focusSkill, 'Avoiding feedback', 'Guessing instead of verifying', 'Working in isolation'], 'correct_index' => 0],
            ['question' => 'What makes a strong answer in a role interview?', 'options' => ['Clear examples and measurable impact', 'Long unrelated stories', 'Only buzzwords', 'No examples at all'], 'correct_index' => 0],
            ['question' => 'How should you respond when you are unsure about a task?', 'options' => ['Ask a precise question and clarify assumptions', 'Ignore it and hope for the best', 'Change the topic', 'Say you already know everything'], 'correct_index' => 0],
            ['question' => 'Which habit usually improves performance fastest?', 'options' => ['Reviewing feedback and iterating', 'Working without reflection', 'Skipping practice', 'Avoiding collaboration'], 'correct_index' => 0],
            ['question' => "What should you connect your answer to for a {$jobTitle} interview?", 'options' => [$industry !== '' ? $industry : 'the team and business goals', 'random personal trivia', 'unrelated hobbies', 'nothing at all'], 'correct_index' => 0],
        ],
    ];
}

$userPrompt = study_buddy_build_prompt($type, $jobTitle, $jobLevel, $industry, $skills, $jobContext);
$aiResult = null;
$aiModel = '';
$openRouterErrors = [];

if ($openRouterToken === '') {
    $openRouterErrors[] = 'OPENROUTER_API_KEY missing; using local fallback payload.';
} else {
    foreach (STUDY_BUDDY_OPENROUTER_MODELS as $candidateModel) {
        $aiResult = study_buddy_call_openrouter($userPrompt, $openRouterToken, $candidateModel);
        if (!empty($aiResult['ok'])) {
            $aiModel = $candidateModel;
            break;
        }

        $openRouterErrors[] = $candidateModel . ': ' . (string) ($aiResult['error'] ?? 'Unknown error');
    }
}

$normalizedContent = null;
$generationSource = 'ai';
if (empty($aiResult['ok'])) {
    $generationSource = 'fallback';
    error_log('[StudyBuddy] All OpenRouter attempts failed for user ' . $userId . ': ' . implode(' | ', $openRouterErrors));
    $normalizedContent = study_buddy_build_local_payload($type, $jobTitle, $jobLevel, $industry, $skills, $jobContext);
} else {
    $aiContent = trim((string) $aiResult['content']);
    $decodedContent = study_buddy_parse_model_json($aiContent);

    if (!is_array($decodedContent)) {
        $generationSource = 'fallback';
        error_log('[StudyBuddy] Invalid JSON from OpenRouter model ' . $aiModel . ' for user ' . $userId . '.');
        $openRouterErrors[] = $aiModel . ': invalid JSON response';
        $normalizedContent = study_buddy_build_local_payload($type, $jobTitle, $jobLevel, $industry, $skills, $jobContext);
    } else {
        $normalizedContent = study_buddy_normalize_payload($decodedContent, $type, $jobTitle);
        if (!is_array($normalizedContent)) {
            $generationSource = 'fallback';
            error_log('[StudyBuddy] Could not normalize OpenRouter output from model ' . $aiModel . ' for user ' . $userId . '.');
            $openRouterErrors[] = $aiModel . ': output could not be normalized';
            $normalizedContent = study_buddy_build_local_payload($type, $jobTitle, $jobLevel, $industry, $skills, $jobContext);
        }
    }
}

$generationWarning = $generationSource === 'fallback'
    ? study_buddy_build_fallback_warning($openRouterErrors)
    : '';

$normalizedJson = json_encode($normalizedContent, JSON_UNESCAPED_UNICODE);
if ($normalizedJson === false) {
    json_error('StudyBuddy could not format the generated content safely. Please try again.');
}

$materialId = null;
try {
    $pdo = get_db();
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `study_materials` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `material_type` VARCHAR(32) NOT NULL,
            `job_title` VARCHAR(255) NOT NULL,
            `job_level` VARCHAR(50) DEFAULT NULL,
            `content_json` LONGTEXT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_study_materials_user_created` (`user_id`, `created_at`),
            CONSTRAINT `fk_study_materials_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $stmt = $pdo->prepare(
        'INSERT INTO study_materials (user_id, material_type, job_title, job_level, content_json)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $type,
        $jobTitle,
        $jobLevel,
        $normalizedJson,
    ]);
    $materialId = (int) $pdo->lastInsertId();
} catch (PDOException $e) {
    error_log('[StudyBuddy] Failed to persist generated material for user ' . $userId . ': ' . $e->getMessage());
}

json_response([
    'success' => true,
    'material_id' => $materialId,
    'source' => $generationSource,
    'warning' => $generationWarning !== '' ? $generationWarning : null,
    'data' => $normalizedContent,
    // Optional debug output: enable in .env with STUDY_BUDDY_DEBUG=1
    'openrouter_debug' => app_env('STUDY_BUDDY_DEBUG', '') === '1' ? [
        'model_tried' => $aiModel,
        'openrouter_errors' => $openRouterErrors,
        'openrouter_raw' => $aiResult['raw'] ?? null,
    ] : null,
]);
