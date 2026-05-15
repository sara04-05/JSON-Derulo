<?php
/**
 * ElevUra — page bootstrap (sessions, user, dashboard data for SSR)
 */
declare(strict_types=1);

require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/../backend/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

require_once __DIR__ . '/helpers.php';

/** @var bool */
$loggedIn = !empty($_SESSION['user_id']);

/** @var array<string, mixed>|null */
$currentUser = null;

/** @var array<string, mixed> */
$dashboardData = [
    'cvs'             => [],
    'applied_jobs'    => [],
    'courses'         => [],
    'mock_interviews' => [],
    'analytics'       => [],
];

/** @var string login|signup|'' */
$authPrompt = '';
if (isset($_GET['auth']) && in_array($_GET['auth'], ['login', 'signup'], true)) {
    $authPrompt = $_GET['auth'];
}

function pick_default_avatar(string $seed): string
{
    $avatars = DEFAULT_AVATARS;
    $n = abs(crc32($seed));
    return $avatars[$n % count($avatars)];
}

function format_page_user(array $row): array
{
    $avatar = $row['avatar'] ?? null;
    if (!$avatar) {
        $avatar = pick_default_avatar($row['username'] ?? $row['email'] ?? 'user');
    }
    return [
        'id'              => (int) $row['id'],
        'username'        => $row['username'],
        'email'           => $row['email'],
        'avatar'          => $avatar,
        'tier'            => $row['membership_tier'],
        'membership_tier' => $row['membership_tier'],
        'loggedIn'        => true,
    ];
}

function load_page_user(int $userId): ?array
{
    try {
        $pdo = get_db();
        $stmt = $pdo->prepare(
            'SELECT id, username, email, avatar, membership_tier, created_at
             FROM users WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? format_page_user($row) : null;
    } catch (PDOException) {
        return null;
    }
}

function load_dashboard_for_user(int $userId): array
{
    $empty = [
        'cvs'             => [],
        'applied_jobs'    => [],
        'courses'         => [],
        'mock_interviews' => [],
        'analytics'       => [
            'overall_score'       => 0,
            'confidence_score'    => 0,
            'communication_score' => 0,
            'ai_feedback'         => 'Complete a mock interview to receive AI feedback.',
            'score_trend'         => [],
        ],
    ];

    try {
        $pdo = get_db();

        $cvsStmt = $pdo->prepare(
            'SELECT id, cv_title, ats_score, file_path, created_at FROM cvs WHERE user_id = ? ORDER BY created_at DESC'
        );
        $cvsStmt->execute([$userId]);
        $cvs = [];
        foreach ($cvsStmt->fetchAll() as $row) {
            $cvs[] = [
                'id'         => (int) $row['id'],
                'title'      => $row['cv_title'],
                'score'      => (int) $row['ats_score'],
                'file_path'  => $row['file_path'],
                'edited'     => date('M j, Y', strtotime($row['created_at'])),
                'created_at' => $row['created_at'],
            ];
        }

        $jobsStmt = $pdo->prepare(
            'SELECT id, company_name, role_name, status, applied_at FROM applied_jobs WHERE user_id = ? ORDER BY applied_at DESC'
        );
        $jobsStmt->execute([$userId]);
        $jobs = [];
        foreach ($jobsStmt->fetchAll() as $row) {
            $jobs[] = [
                'id'      => (int) $row['id'],
                'company' => $row['company_name'],
                'role'    => $row['role_name'],
                'status'  => $row['status'],
                'date'    => date('M j, Y', strtotime($row['applied_at'])),
            ];
        }

        $coursesStmt = $pdo->prepare(
            'SELECT id, course_name, progress_percent, completed_at FROM completed_courses WHERE user_id = ? ORDER BY id ASC'
        );
        $coursesStmt->execute([$userId]);
        $courses = [];
        foreach ($coursesStmt->fetchAll() as $row) {
            $progress = (int) $row['progress_percent'];
            $done = $progress >= 100;
            $courses[] = [
                'id'       => (int) $row['id'],
                'name'     => $row['course_name'],
                'progress' => $progress,
                'status'   => $done ? 'Completed' : 'In Progress',
                'badge'    => $done ? 'Mastered' : ($progress >= 70 ? 'On Track' : 'Building'),
            ];
        }

        $miStmt = $pdo->prepare(
            'SELECT id, interview_score, confidence_score, communication_score, ai_feedback, created_at
             FROM mock_interviews WHERE user_id = ? ORDER BY created_at DESC LIMIT 10'
        );
        $miStmt->execute([$userId]);
        $interviews = [];
        foreach ($miStmt->fetchAll() as $row) {
            $interviews[] = [
                'id'                  => (int) $row['id'],
                'interview_score'     => (int) $row['interview_score'],
                'confidence_score'    => (int) $row['confidence_score'],
                'communication_score' => (int) $row['communication_score'],
                'ai_feedback'         => $row['ai_feedback'],
                'created_at'          => $row['created_at'],
                'date'                => date('M j, Y', strtotime($row['created_at'])),
            ];
        }

        $latest = $interviews[0] ?? null;

        return [
            'cvs'             => $cvs,
            'applied_jobs'    => $jobs,
            'courses'         => $courses,
            'mock_interviews' => $interviews,
            'analytics'       => [
                'overall_score'       => $latest['interview_score'] ?? 0,
                'confidence_score'    => $latest['confidence_score'] ?? 0,
                'communication_score' => $latest['communication_score'] ?? 0,
                'ai_feedback'         => $latest['ai_feedback'] ?? $empty['analytics']['ai_feedback'],
                'score_trend'         => array_column($interviews, 'interview_score'),
            ],
        ];
    } catch (PDOException) {
        return $empty;
    }
}

if ($loggedIn) {
    $userId = (int) $_SESSION['user_id'];
    $currentUser = load_page_user($userId);
    if (!$currentUser) {
        $_SESSION = [];
        $loggedIn = false;
    } else {
        $dashboardData = load_dashboard_for_user($userId);
    }
}

$initialPayload = [
    'logged_in' => $loggedIn,
    'user'      => $currentUser,
    'dashboard' => $loggedIn ? $dashboardData : null,
];

$pageTitle = 'ElevUra Dashboard - AI Command Center';
