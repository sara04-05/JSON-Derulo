<?php
/**
 * ElevUra — persist mock interview score and feedback
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

$userId = require_auth();
$input = read_json_body();

$jobTitle = trim((string) ($input['job_title'] ?? ''));
$interviewScore = (int) ($input['interview_score'] ?? 0);
$tier = trim((string) ($input['tier'] ?? ''));
$summary = trim((string) ($input['summary'] ?? ''));
$items = $input['items'] ?? [];

if ($jobTitle === '') {
    json_error('Job title is required.');
}

if (!is_array($items) || $items === []) {
    json_error('Interview feedback items are required.');
}

$interviewScore = max(0, min(100, $interviewScore));

$normalizedItems = [];
$itemScores = [];

foreach ($items as $item) {
    if (!is_array($item)) {
        continue;
    }

    $question = trim((string) ($item['question'] ?? ''));
    $answer = trim((string) ($item['answer'] ?? ''));
    $score = max(0, min(100, (int) ($item['score'] ?? 0)));
    $feedback = trim((string) ($item['feedback'] ?? ''));

    if ($question === '') {
        continue;
    }

    $normalizedItems[] = [
        'question' => $question,
        'answer'   => $answer,
        'score'    => $score,
        'feedback' => $feedback,
    ];
    $itemScores[] = $score;
}

if ($normalizedItems === []) {
    json_error('No valid interview answers were provided.');
}

if ($interviewScore === 0 && $itemScores !== []) {
    $interviewScore = (int) round(array_sum($itemScores) / count($itemScores));
}

$communicationScore = $itemScores === []
    ? $interviewScore
    : (int) round(array_sum($itemScores) / count($itemScores));

$confidenceScore = mock_interview_derive_confidence_score($normalizedItems, $interviewScore);

$feedbackPayload = [
    'job_title' => $jobTitle,
    'tier'      => $tier,
    'summary'   => $summary,
    'items'     => $normalizedItems,
];

$aiFeedback = json_encode($feedbackPayload, JSON_UNESCAPED_UNICODE);
if ($aiFeedback === false) {
    json_error('Could not format interview feedback for storage.');
}

try {
    $pdo = get_db();
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS `mock_interviews` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT UNSIGNED NOT NULL,
            `interview_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `confidence_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `communication_score` TINYINT UNSIGNED NOT NULL DEFAULT 0,
            `ai_feedback` TEXT,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_mock_interviews_user_id` (`user_id`),
            CONSTRAINT `fk_mock_interviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $stmt = $pdo->prepare(
        'INSERT INTO mock_interviews
            (user_id, interview_score, confidence_score, communication_score, ai_feedback)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $userId,
        $interviewScore,
        $confidenceScore,
        $communicationScore,
        $aiFeedback,
    ]);

    $interviewId = (int) $pdo->lastInsertId();
} catch (PDOException $e) {
    error_log('[MockInterview] Failed to save interview for user ' . $userId . ': ' . $e->getMessage());
    json_error('Could not save interview results. Please try again.', 500);
}

json_response([
    'success' => true,
    'interview_id' => $interviewId,
    'interview_score' => $interviewScore,
    'confidence_score' => $confidenceScore,
    'communication_score' => $communicationScore,
]);

/**
 * @param list<array{question: string, answer: string, score: int, feedback: string}> $items
 */
function mock_interview_derive_confidence_score(array $items, int $overallScore): int
{
    $score = $overallScore;

    foreach ($items as $item) {
        $answer = $item['answer'];

        if ($answer === '[Skipped]' || $answer === '') {
            $score -= 12;
            continue;
        }

        $wordCount = str_word_count($answer);
        if ($wordCount < 12) {
            $score -= 8;
        } elseif ($wordCount >= 35) {
            $score += 3;
        }

        $fillers = preg_match_all('/\b(um|uh|erm|like|you know)\b/i', $answer);
        if ($fillers > 0) {
            $score -= min(16, $fillers * 3);
        }
    }

    return max(0, min(100, (int) round($score)));
}
