<?php

namespace Leart\JsonDerulo\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeminiService
{
    private Client $client;
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=AIzaSyDLHenQ24HWWAgGhTVb7Cqe38j5cJ02B94';

    public function __construct()
    {
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-1.5-flash';
        $this->client = new Client([
            'timeout' => 90,
            'connect_timeout' => 15,
        ]);
    }

    /**
     * Generate a text response from Gemini.
     *
     * @param string $prompt       User prompt
     * @param string $systemPrompt System instruction
     * @param array  $history      Prior conversation turns [{role, text}, ...]
     * @return array {success: bool, text: string} | {error: bool, message: string}
     */
    public function generate(string $prompt, string $systemPrompt = '', array $history = []): array
    {
        if (empty($this->apiKey)) {
            return ['error' => true, 'message' => 'Gemini API key not configured. Add GEMINI_API_KEY to .env'];
        }

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $contents = [];
        foreach ($history as $entry) {
            $role = ($entry['role'] === 'assistant' || $entry['role'] === 'model') ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $entry['text']]]
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ];

        $body = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8192,
            ]
        ];

        if (!empty($systemPrompt)) {
            $body['systemInstruction'] = [
                'parts' => [['text' => $systemPrompt]]
            ];
        }

        try {
            $response = $this->client->post($url, [
                'json' => $body,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => true, 'message' => 'Invalid JSON response from API'];
            }

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text !== null) {
                return ['success' => true, 'text' => $text];
            }

            $blockReason = $data['promptFeedback']['blockReason'] ?? null;
            if ($blockReason) {
                return ['error' => true, 'message' => "Request blocked: {$blockReason}"];
            }

            return ['error' => true, 'message' => 'No response generated'];
        } catch (GuzzleException $e) {
            $msg = $e->getMessage();
            // Never leak the API key in error messages
            $msg = str_replace($this->apiKey, '[REDACTED]', $msg);
            return ['error' => true, 'message' => 'API request failed: ' . $msg];
        }
    }

    /**
     * Generate a structured JSON response from Gemini.
     *
     * @param string $prompt       User prompt (should describe the JSON structure)
     * @param string $systemPrompt System instruction
     * @return array {success: bool, data: array} | {error: bool, message: string}
     */
    public function generateJson(string $prompt, string $systemPrompt = ''): array
    {
        if (empty($this->apiKey)) {
            return ['error' => true, 'message' => 'Gemini API key not configured. Add GEMINI_API_KEY to .env'];
        }

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}";

        $body = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8192,
                'responseMimeType' => 'application/json'
            ]
        ];

        if (!empty($systemPrompt)) {
            $body['systemInstruction'] = [
                'parts' => [['text' => $systemPrompt]]
            ];
        }

        try {
            $response = $this->client->post($url, [
                'json' => $body,
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => true, 'message' => 'Invalid JSON response from API'];
            }

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            if ($text === null) {
                return ['error' => true, 'message' => 'No response generated'];
            }

            // Parse the JSON text from the model
            $parsed = json_decode($text, true);
            if (json_last_error() === JSON_ERROR_NONE && $parsed !== null) {
                return ['success' => true, 'data' => $parsed];
            }

            // Fallback: try to extract JSON from markdown code block
            if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $m)) {
                $parsed = json_decode(trim($m[1]), true);
                if (json_last_error() === JSON_ERROR_NONE && $parsed !== null) {
                    return ['success' => true, 'data' => $parsed];
                }
            }

            // Return raw text if JSON parsing fails
            return ['success' => true, 'data' => null, 'text' => $text];
        } catch (GuzzleException $e) {
            $msg = $e->getMessage();
            $msg = str_replace($this->apiKey, '[REDACTED]', $msg);
            return ['error' => true, 'message' => 'API request failed: ' . $msg];
        }
    }
}
