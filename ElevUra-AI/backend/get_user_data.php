<?php
/**
 * ElevUra — session check + user dashboard data
 */
declare(strict_types=1);

require_once __DIR__ . '/auth_check.php';

$userId = current_user_id();

if (!$userId) {
    json_response([
        'success'   => true,
        'logged_in' => false,
        'user'      => null,
    ]);
}

try {
    $pdo = get_db();
    $userRow = fetch_user_by_id($userId);

    if (!$userRow) {
        $_SESSION = [];
        json_response(['success' => true, 'logged_in' => false, 'user' => null]);
    }

    $cvsStmt = $pdo->prepare(
        'SELECT id, cv_title, ats_score, file_path, created_at
         FROM cvs WHERE user_id = ? ORDER BY created_at DESC'
    );
    $cvsStmt->execute([$userId]);
    $cvs = array_map(static function (array $row): array {
        return [
            'id'         => (int) $row['id'],
            'title'      => $row['cv_title'],
            'score'      => (int) $row['ats_score'],
            'file_path'  => $row['file_path'],
            'edited'     => date('M j, Y', strtotime($row['created_at'])),
            'created_at' => $row['created_at'],
        ];
    }, $cvsStmt->fetchAll());

    $jobsStmt = $pdo->prepare(
        'SELECT id, company_name, role_name, status, applied_at
         FROM applied_jobs WHERE user_id = ? ORDER BY applied_at DESC'
    );
    $jobsStmt->execute([$userId]);
    $jobs = array_map(static function (array $row): array {
        return [
            'id'      => (int) $row['id'],
            'company' => $row['company_name'],
            'role'    => $row['role_name'],
            'status'  => $row['status'],
            'date'    => date('M j, Y', strtotime($row['applied_at'])),
        ];
    }, $jobsStmt->fetchAll());

    $coursesStmt = $pdo->prepare(
        'SELECT id, course_name, progress_percent, completed_at
         FROM completed_courses WHERE user_id = ? ORDER BY id ASC'
    );
    $coursesStmt->execute([$userId]);
    $courses = array_map(static function (array $row): array {
        $progress = (int) $row['progress_percent'];
        $done = $progress >= 100;
        return [
            'id'       => (int) $row['id'],
            'name'     => $row['course_name'],
            'progress' => $progress,
            'status'   => $done ? 'Completed' : 'In Progress',
            'badge'    => $done ? 'Mastered' : ($progress >= 70 ? 'On Track' : 'Building'),
        ];
    }, $coursesStmt->fetchAll());

    $miStmt = $pdo->prepare(
        'SELECT id, interview_score, confidence_score, communication_score, ai_feedback, created_at
         FROM mock_interviews WHERE user_id = ? ORDER BY created_at DESC LIMIT 10'
    );
    $miStmt->execute([$userId]);
    $interviews = array_map(static function (array $row): array {
        return [
            'id'                   => (int) $row['id'],
            'interview_score'      => (int) $row['interview_score'],
            'confidence_score'     => (int) $row['confidence_score'],
            'communication_score'  => (int) $row['communication_score'],
            'ai_feedback'          => $row['ai_feedback'],
            'created_at'           => $row['created_at'],
            'date'                 => date('M j, Y', strtotime($row['created_at'])),
        ];
    }, $miStmt->fetchAll());

    $latest = $interviews[0] ?? null;

    json_response([
        'success'         => true,
        'logged_in'       => true,
        'user'            => format_user_row($userRow),
        'cvs'             => $cvs,
        'applied_jobs'    => $jobs,
        'courses'         => $courses,
        'mock_interviews' => $interviews,
        'analytics'       => [
            'overall_score'       => $latest['interview_score'] ?? 0,
            'confidence_score'    => $latest['confidence_score'] ?? 0,
            'communication_score' => $latest['communication_score'] ?? 0,
            'ai_feedback'         => $latest['ai_feedback'] ?? 'Complete a mock interview to receive AI feedback.',
            'score_trend'         => array_column($interviews, 'interview_score'),
        ],
    ]);
} catch (PDOException $e) {
    json_error('Database connection failed. Import elevura.sql and start MySQL in XAMPP.', 500);
}
