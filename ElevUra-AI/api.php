<?php
/**
 * ElevUra AI — API Entry Point
 *
 * Routes requests to Study Buddy and Research Assistant services.
 * All AI calls happen server-side only. API key is never exposed.
 *
 * Usage: api.php?module=study&action=chat  (POST)
 *        api.php?module=research&action=search (POST)
 */

// CORS headers for local development
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Load Composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Parse request
$module = $_GET['module'] ?? '';
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Parse JSON body for POST requests
$input = [];
if ($method === 'POST') {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $input = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            respond(400, ['error' => true, 'message' => 'Invalid JSON in request body']);
        }
    }
}

// Route to appropriate handler
try {
    switch ($module) {
        case 'study':
            handleStudy($action, $method, $input);
            break;
        case 'research':
            handleResearch($action, $method, $input);
            break;
        default:
            respond(400, ['error' => true, 'message' => 'Unknown module. Use: study, research']);
    }
} catch (\Throwable $e) {
    // Never expose internal details — sanitize the message
    $safeMsg = preg_replace('/AIza[A-Za-z0-9_\-]+/', '[REDACTED]', $e->getMessage());
    respond(500, ['error' => true, 'message' => 'Server error: ' . $safeMsg]);
}

// ─── Study Buddy Routes ──────────────────────────────────────

function handleStudy(string $action, string $method, array $input): void
{
    $svc = new \Leart\JsonDerulo\Services\StudyBuddyService();

    switch ($action) {
        case 'chat':
            requirePost($method);
            $sessionId = $input['sessionId'] ?? ('session_' . bin2hex(random_bytes(8)));
            $message = trim($input['message'] ?? '');
            if (empty($message)) {
                respond(400, ['error' => true, 'message' => 'Message is required']);
            }
            respond(200, $svc->chat($sessionId, $message));
            break;

        case 'explain':
            requirePost($method);
            $topic = trim($input['topic'] ?? '');
            if (empty($topic)) {
                respond(400, ['error' => true, 'message' => 'Topic is required']);
            }
            $difficulty = $input['difficulty'] ?? 'intermediate';
            $goal = $input['goal'] ?? 'understand';
            respond(200, $svc->explain($topic, $difficulty, $goal));
            break;

        case 'quiz':
            requirePost($method);
            $topic = trim($input['topic'] ?? '');
            if (empty($topic)) {
                respond(400, ['error' => true, 'message' => 'Topic is required']);
            }
            $count = intval($input['count'] ?? 5);
            $difficulty = $input['difficulty'] ?? 'intermediate';
            $types = $input['types'] ?? ['multiple_choice'];
            respond(200, $svc->generateQuiz($topic, $count, $difficulty, $types));
            break;

        case 'flashcards':
            requirePost($method);
            $topic = trim($input['topic'] ?? '');
            if (empty($topic)) {
                respond(400, ['error' => true, 'message' => 'Topic is required']);
            }
            $count = intval($input['count'] ?? 10);
            $difficulty = $input['difficulty'] ?? 'intermediate';
            respond(200, $svc->generateFlashcards($topic, $count, $difficulty));
            break;

        case 'plan':
            requirePost($method);
            $goal = trim($input['goal'] ?? '');
            if (empty($goal)) {
                respond(400, ['error' => true, 'message' => 'Goal is required']);
            }
            $intensity = $input['intensity'] ?? 'focused';
            $topics = $input['topics'] ?? '';
            $weeks = intval($input['weeks'] ?? 4);
            respond(200, $svc->generateStudyPlan($goal, $intensity, $topics, $weeks));
            break;

        case 'history':
            requireGet($method);
            $sessionId = $_GET['sessionId'] ?? '';
            if (empty($sessionId)) {
                respond(400, ['error' => true, 'message' => 'sessionId is required']);
            }
            respond(200, $svc->getChatHistory($sessionId));
            break;

        default:
            respond(400, ['error' => true, 'message' => 'Unknown study action. Use: chat, explain, quiz, flashcards, plan, history']);
    }
}

// ─── Research Assistant Routes ────────────────────────────────

function handleResearch(string $action, string $method, array $input): void
{
    $svc = new \Leart\JsonDerulo\Services\ResearchService();

    switch ($action) {
        case 'search':
            requirePost($method);
            $query = trim($input['query'] ?? '');
            if (empty($query)) {
                respond(400, ['error' => true, 'message' => 'Search query is required']);
            }
            $location = $input['location'] ?? '';
            $level = $input['level'] ?? '';
            $type = $input['type'] ?? '';
            respond(200, $svc->searchJobs($query, $location, $level, $type));
            break;

        case 'skills':
            requirePost($method);
            $text = trim($input['text'] ?? '');
            if (empty($text)) {
                respond(400, ['error' => true, 'message' => 'Text is required for skill extraction']);
            }
            respond(200, $svc->extractSkills($text));
            break;

        case 'compare':
            requirePost($method);
            $roles = $input['roles'] ?? [];
            if (count($roles) < 2) {
                respond(400, ['error' => true, 'message' => 'At least 2 roles are required for comparison']);
            }
            respond(200, $svc->compareRoles($roles));
            break;

        case 'save':
            requirePost($method);
            respond(200, $svc->saveSearch($input));
            break;

        case 'history':
            requireGet($method);
            respond(200, $svc->getHistory());
            break;

        case 'delete':
            requirePost($method);
            $id = $input['id'] ?? '';
            if (empty($id)) {
                respond(400, ['error' => true, 'message' => 'Search id is required']);
            }
            respond(200, $svc->deleteSearch($id));
            break;

        default:
            respond(400, ['error' => true, 'message' => 'Unknown research action. Use: search, skills, compare, save, history, delete']);
    }
}

// ─── Helpers ──────────────────────────────────────────────────

function respond(int $statusCode, array $data): void
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function requirePost(string $method): void
{
    if ($method !== 'POST') {
        respond(405, ['error' => true, 'message' => 'POST method required']);
    }
}

function requireGet(string $method): void
{
    if ($method !== 'GET') {
        respond(405, ['error' => true, 'message' => 'GET method required']);
    }
}
