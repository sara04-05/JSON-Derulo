<?php

namespace Leart\JsonDerulo\Services;

class StudyBuddyService
{
    private GeminiService $gemini;
    private StorageService $storage;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->storage = new StorageService('study_sessions');
    }

    /**
     * Chat with AI study buddy. Maintains conversation history.
     */
    public function chat(string $sessionId, string $message): array
    {
        $system = "You are Study Buddy, an intelligent and encouraging AI tutor. "
            . "You help students learn, understand concepts, solve problems, and prepare for exams. "
            . "Be clear, thorough, and supportive. Use examples, analogies, and step-by-step explanations. "
            . "Format your responses with markdown for readability. "
            . "Keep responses concise but complete.";

        // Load existing conversation
        $session = $this->storage->load($sessionId);
        $history = $session['messages'] ?? [];

        // Build history for Gemini (last 20 messages for context window)
        $contextHistory = array_slice($history, -20);

        $result = $this->gemini->generate($message, $system, $contextHistory);

        if (isset($result['error'])) {
            return $result;
        }

        // Save messages to history
        $this->storage->append($sessionId, 'messages', [
            'role' => 'user',
            'text' => $message,
            'time' => date('c')
        ]);
        $this->storage->append($sessionId, 'messages', [
            'role' => 'model',
            'text' => $result['text'],
            'time' => date('c')
        ]);

        return ['success' => true, 'text' => $result['text'], 'sessionId' => $sessionId];
    }

    /**
     * Get chat history for a session.
     */
    public function getChatHistory(string $sessionId): array
    {
        $session = $this->storage->load($sessionId);
        return [
            'success' => true,
            'messages' => $session['messages'] ?? [],
            'sessionId' => $sessionId
        ];
    }

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

    /**
     * Generate quiz questions (returns structured JSON).
     */
    public function generateQuiz(string $topic, int $count = 5, string $difficulty = 'intermediate', array $types = ['multiple_choice']): array
    {
        $typeStr = implode(', ', $types);
        $system = "You are a quiz generator. Create engaging, educational quiz questions. "
            . "Return valid JSON only with this exact structure:\n"
            . '{"quiz_title":"...","topic":"...","difficulty":"...","questions":[{"id":1,"type":"multiple_choice","question":"...","options":["A)...","B)...","C)...","D)..."],"correct_answer":"B","explanation":"..."}]}' . "\n"
            . "For true_false type, options should be [\"True\",\"False\"]. "
            . "For fill_blank type, omit options and add \"answer\":\"...\". "
            . "Always include an explanation for each answer.";

        $prompt = "Generate a quiz with exactly {$count} questions about: \"{$topic}\"\n"
            . "Difficulty: {$difficulty}\n"
            . "Question types: {$typeStr}\n"
            . "Make questions progressively challenging.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'quiz' => $result['data']];
        }

        return ['error' => true, 'message' => 'Failed to generate structured quiz'];
    }

    /**
     * Generate flashcards (returns structured JSON).
     */
    public function generateFlashcards(string $topic, int $count = 10, string $difficulty = 'intermediate'): array
    {
        $system = "You are a flashcard generator for effective spaced-repetition learning. "
            . "Return valid JSON only with this exact structure:\n"
            . '{"title":"...","topic":"...","card_count":10,"cards":[{"id":1,"front":"Question or concept","back":"Answer or explanation","hint":"Optional hint","difficulty":"easy|medium|hard","category":"..."}]}' . "\n"
            . "Front should be a clear question or concept. Back should be a concise, memorable answer.";

        $prompt = "Generate exactly {$count} flashcards about: \"{$topic}\"\n"
            . "Difficulty level: {$difficulty}\n"
            . "Mix of difficulties within cards. Cover key concepts comprehensively.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'flashcards' => $result['data']];
        }

        return ['error' => true, 'message' => 'Failed to generate flashcards'];
    }

    /**
     * Generate a study plan (returns structured JSON).
     */
    public function generateStudyPlan(string $goal, string $intensity = 'focused', string $topics = '', int $weeks = 4): array
    {
        $intensityMap = [
            'casual' => '30 minutes per day, relaxed pace',
            'focused' => '90 minutes per day, steady progress',
            'intensive' => '3+ hours per day, accelerated learning'
        ];
        $paceDesc = $intensityMap[$intensity] ?? $intensityMap['focused'];

        $system = "You are a study plan architect. Create detailed, actionable study plans. "
            . "Return valid JSON only with this exact structure:\n"
            . '{"plan_title":"...","goal":"...","total_weeks":4,"intensity":"...","daily_time":"...","weeks":[{"week":1,"title":"...","focus":"...","days":[{"day":1,"topic":"...","duration":"90 min","activities":["Read chapter...","Practice..."]}]}]}' . "\n"
            . "Each week should have 5-6 study days. Activities should be specific and actionable.";

        $prompt = "Create a {$weeks}-week study plan.\n"
            . "Goal: {$goal}\n"
            . "Intensity: {$intensity} ({$paceDesc})\n"
            . (!empty($topics) ? "Topics to cover: {$topics}\n" : "")
            . "Make it progressive, building from fundamentals to advanced concepts.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'plan' => $result['data']];
        }

        return ['error' => true, 'message' => 'Failed to generate study plan'];
    }
}
