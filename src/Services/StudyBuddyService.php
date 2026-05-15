<?php

namespace Leart\JsonDerulo\Services;

use PDO;

/**
 * StudyBuddyService — Chat-based AI tutor
 *
 * Supports: chat, explain, quiz, flashcards, study plans.
 * Stores conversation history in MySQL (study_sessions + study_messages).
 * All AI calls use GeminiService (plain text generation).
 */
class StudyBuddyService
{
    private GeminiService $gemini;
    private PDO $db;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->db     = DatabaseService::getConnection();
    }

    // ─── Chat ────────────────────────────────────────────────

    /**
     * Chat with the AI study buddy. Saves messages to MySQL.
     */
    public function chat(string $sessionId, string $message): array
    {
        $system = "You are Study Buddy, an intelligent and encouraging AI tutor. "
            . "You help students learn, understand concepts, solve problems, and prepare for exams. "
            . "Be clear, thorough, and supportive. Use examples, analogies, and step-by-step explanations. "
            . "Format your responses with markdown for readability. "
            . "Keep responses concise but complete.";

        // Ensure the session exists
        $this->ensureSession($sessionId);

        // Load the last 20 messages for context
        $history = $this->loadMessages($sessionId, 20);

        // Call Gemini
        $result = $this->gemini->generate($message, $system, $history);

        if (isset($result['error'])) {
            return $result;
        }

        // Save user message
        $this->saveMessage($sessionId, 'user', $message);

        // Save model reply
        $this->saveMessage($sessionId, 'model', $result['text']);

        // Touch session updated_at
        $this->touchSession($sessionId);

        return [
            'success'   => true,
            'text'      => $result['text'],
            'sessionId' => $sessionId,
        ];
    }

    /**
     * Get chat history for a session.
     */
    public function getChatHistory(string $sessionId): array
    {
        $messages = $this->loadMessages($sessionId, 200);
        return [
            'success'   => true,
            'messages'  => $messages,
            'sessionId' => $sessionId,
        ];
    }

    // ─── Explain ─────────────────────────────────────────────

    /**
     * Generate a topic explanation.
     */
    public function explain(string $topic, string $difficulty = 'intermediate', string $goal = 'understand'): array
    {
        $system = "You are an expert educator. Provide a comprehensive, well-structured explanation. "
            . "Use markdown formatting with headers, bullet points, and code blocks where appropriate. "
            . "Structure your response as:\n"
            . "## Overview\nBrief introduction\n"
            . "## Key Concepts\nCore ideas with clear definitions\n"
            . "## Detailed Explanation\nIn-depth walkthrough\n"
            . "## Examples\nPractical, illustrative examples\n"
            . "## Common Pitfalls\nMistakes to avoid\n"
            . "## Summary\nKey takeaways";

        $prompt = "Explain the topic: \"{$topic}\"\n"
            . "Difficulty level: {$difficulty}\n"
            . "Learning goal: {$goal}\n"
            . "Provide a thorough explanation appropriate for this level.";

        return $this->gemini->generate($prompt, $system);
    }

    // ─── Quiz ────────────────────────────────────────────────

    /**
     * Generate quiz questions. Returns structured JSON via prompt-based parsing.
     */
    public function generateQuiz(string $topic, int $count = 5, string $difficulty = 'intermediate', array $types = ['multiple_choice']): array
    {
        $typeStr = implode(', ', $types);

        $system = "You are a quiz generator. Create engaging, educational quiz questions. "
            . "You MUST return ONLY valid JSON with this exact structure (no other text, no markdown):\n"
            . '{"quiz_title":"...","topic":"...","difficulty":"...","questions":[{"id":1,"type":"multiple_choice","question":"...","options":["A)...","B)...","C)...","D)..."],"correct_answer":"B","explanation":"..."}]}' . "\n"
            . "For true_false type, options should be [\"True\",\"False\"]. "
            . "For fill_blank type, omit options and add \"answer\":\"...\". "
            . "Always include an explanation for each answer.";

        $prompt = "Generate a quiz with exactly {$count} questions about: \"{$topic}\"\n"
            . "Difficulty: {$difficulty}\n"
            . "Question types: {$typeStr}\n"
            . "Make questions progressively challenging.\n"
            . "Reply with valid JSON only.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'quiz' => $result['data']];
        }

        // Fallback: return raw text if JSON parsing failed
        return ['success' => true, 'quiz' => null, 'text' => $result['text'] ?? 'Failed to generate structured quiz'];
    }

    // ─── Flashcards ──────────────────────────────────────────

    /**
     * Generate flashcards. Returns structured JSON via prompt-based parsing.
     */
    public function generateFlashcards(string $topic, int $count = 10, string $difficulty = 'intermediate'): array
    {
        $system = "You are a flashcard generator for effective spaced-repetition learning. "
            . "You MUST return ONLY valid JSON with this exact structure (no other text, no markdown):\n"
            . '{"title":"...","topic":"...","card_count":10,"cards":[{"id":1,"front":"Question or concept","back":"Answer or explanation","hint":"Optional hint","difficulty":"easy|medium|hard","category":"..."}]}' . "\n"
            . "Front should be a clear question or concept. Back should be a concise, memorable answer.";

        $prompt = "Generate exactly {$count} flashcards about: \"{$topic}\"\n"
            . "Difficulty level: {$difficulty}\n"
            . "Mix of difficulties within cards. Cover key concepts comprehensively.\n"
            . "Reply with valid JSON only.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'flashcards' => $result['data']];
        }

        return ['success' => true, 'flashcards' => null, 'text' => $result['text'] ?? 'Failed to generate flashcards'];
    }

    // ─── Study Plan ──────────────────────────────────────────

    /**
     * Generate a study plan. Returns structured JSON via prompt-based parsing.
     */
    public function generateStudyPlan(string $goal, string $intensity = 'focused', string $topics = '', int $weeks = 4): array
    {
        $intensityMap = [
            'casual'    => '30 minutes per day, relaxed pace',
            'focused'   => '90 minutes per day, steady progress',
            'intensive' => '3+ hours per day, accelerated learning',
        ];
        $paceDesc = $intensityMap[$intensity] ?? $intensityMap['focused'];

        $system = "You are a study plan architect. Create detailed, actionable study plans. "
            . "You MUST return ONLY valid JSON with this exact structure (no other text, no markdown):\n"
            . '{"plan_title":"...","goal":"...","total_weeks":4,"intensity":"...","daily_time":"...","weeks":[{"week":1,"title":"...","focus":"...","days":[{"day":1,"topic":"...","duration":"90 min","activities":["Read chapter...","Practice..."]}]}]}' . "\n"
            . "Each week should have 5-6 study days. Activities should be specific and actionable.";

        $prompt = "Create a {$weeks}-week study plan.\n"
            . "Goal: {$goal}\n"
            . "Intensity: {$intensity} ({$paceDesc})\n"
            . (!empty($topics) ? "Topics to cover: {$topics}\n" : "")
            . "Make it progressive, building from fundamentals to advanced concepts.\n"
            . "Reply with valid JSON only.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'plan' => $result['data']];
        }

        return ['success' => true, 'plan' => null, 'text' => $result['text'] ?? 'Failed to generate study plan'];
    }

    // ─── MySQL Helpers ───────────────────────────────────────

    /**
     * Ensure a study session row exists.
     */
    private function ensureSession(string $sessionId): void
    {
        $sql = "INSERT IGNORE INTO `study_sessions` (id, created_at, updated_at)
                VALUES (:id, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $sessionId]);
    }

    /**
     * Update the session's updated_at timestamp.
     */
    private function touchSession(string $sessionId): void
    {
        $sql  = "UPDATE `study_sessions` SET updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $sessionId]);
    }

    /**
     * Save a chat message to the study_messages table.
     */
    private function saveMessage(string $sessionId, string $role, string $message): void
    {
        $sql = "INSERT INTO `study_messages` (session_id, role, message, created_at)
                VALUES (:sid, :role, :msg, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':sid'  => $sessionId,
            ':role' => $role,
            ':msg'  => $message,
        ]);
    }

    /**
     * Load messages for a session, ordered chronologically.
     *
     * @return array  Each entry: ['role' => '...', 'text' => '...', 'time' => '...']
     */
    private function loadMessages(string $sessionId, int $limit = 20): array
    {
        $sql = "SELECT role, message AS text, created_at AS time
                FROM `study_messages`
                WHERE session_id = :sid
                ORDER BY id ASC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':sid', $sessionId, \PDO::PARAM_STR);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
