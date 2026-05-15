<?php

namespace Leart\JsonDerulo\Services;

use PDO;

/**
 * ResearchService — AI-powered Job Search & Research Assistant
 *
 * Supports: job search, skill extraction, role comparison, saved searches.
 * Stores results and history in MySQL (research_results + saved_searches).
 * All AI calls use GeminiService (plain text generation).
 */
class ResearchService
{
    private GeminiService $gemini;
    private PDO $db;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->db     = DatabaseService::getConnection();
    }

    // ─── Job Search ──────────────────────────────────────────

    /**
     * Search for jobs using AI-generated structured results.
     */
    public function searchJobs(string $query, string $location = '', string $level = '', string $type = ''): array
    {
        $system = "You are a career research assistant with deep knowledge of the job market. "
            . "Generate realistic, helpful, and detailed job search results. "
            . "You MUST return ONLY valid JSON with this exact structure (no other text, no markdown):\n"
            . '{"query_summary":"...","total_results":8,"results":[{"id":1,"title":"...","company":"...","location":"...","type":"Full-time","salary_range":"$80K-$120K","experience_level":"...","description":"2-3 sentence description","requirements":["req1","req2"],"key_skills":["skill1","skill2"],"match_score":85,"posted_ago":"2 days ago"}],"market_insights":{"demand_level":"High","avg_salary":"...","trending_skills":["..."],"market_summary":"..."}}' . "\n"
            . "Generate 6-8 diverse, realistic results. Match scores should be 60-98. "
            . "Companies should be a mix of well-known and realistic smaller companies.";

        $prompt = "Search for jobs matching: \"{$query}\"";
        if (!empty($location)) $prompt .= "\nLocation: {$location}";
        if (!empty($level))    $prompt .= "\nExperience level: {$level}";
        if (!empty($type))     $prompt .= "\nJob type: {$type}";
        $prompt .= "\nProvide detailed, realistic results with accurate market data."
            . "\nReply with valid JSON only.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        // Save to research_results
        if (isset($result['data'])) {
            $this->saveResult($query, 'job_search', json_encode($result['data'], JSON_UNESCAPED_UNICODE), [
                'location' => $location,
                'level'    => $level,
                'type'     => $type,
            ]);
            return ['success' => true, 'results' => $result['data']];
        }

        // Fallback: return raw text
        return ['success' => true, 'results' => null, 'text' => $result['text'] ?? 'Failed to generate job results'];
    }

    // ─── Skill Extraction ────────────────────────────────────

    /**
     * Extract and categorize skills from a job description or text.
     */
    public function extractSkills(string $text): array
    {
        $system = "You are an expert at analyzing job descriptions and extracting skills. "
            . "You MUST return ONLY valid JSON with this exact structure (no other text, no markdown):\n"
            . '{"summary":"Brief analysis","technical_skills":[{"name":"Python","proficiency":"Advanced","category":"Programming Language"}],"soft_skills":["Communication","Leadership"],"tools_platforms":["AWS","Docker"],"certifications_mentioned":["AWS Solutions Architect"],"experience_requirements":{"min_years":3,"preferred_years":5,"education":"Bachelor\'s in CS"},"skill_gap_tips":["Consider learning..."]}' . "\n"
            . "Be thorough and categorize every skill found in the text.";

        $prompt = "Analyze the following text and extract all skills, requirements, and qualifications:\n\n"
            . $text
            . "\n\nReply with valid JSON only.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            // Save extraction result
            $this->saveResult(
                substr($text, 0, 200),
                'skill_extraction',
                json_encode($result['data'], JSON_UNESCAPED_UNICODE)
            );
            return ['success' => true, 'skills' => $result['data']];
        }

        return ['success' => true, 'skills' => null, 'text' => $result['text'] ?? 'Failed to extract skills'];
    }

    // ─── Role Comparison ─────────────────────────────────────

    /**
     * Compare multiple roles/positions.
     */
    public function compareRoles(array $roles): array
    {
        $roleList = implode(', ', $roles);

        $system = "You are a career advisor who provides detailed role comparisons. "
            . "You MUST return ONLY valid JSON with this exact structure (no other text, no markdown):\n"
            . '{"roles_compared":["Role A","Role B"],"comparison_table":[{"category":"Average Salary","values":{"Role A":"$95K-$130K","Role B":"$85K-$115K"}},{"category":"Growth Potential","values":{"Role A":"High","Role B":"Very High"}},{"category":"Key Skills","values":{"Role A":"Python, SQL","Role B":"JavaScript, React"}},{"category":"Work-Life Balance","values":{"Role A":"Good","Role B":"Moderate"}},{"category":"Market Demand","values":{"Role A":"High","Role B":"Very High"}},{"category":"Remote Opportunities","values":{"Role A":"Common","Role B":"Very Common"}},{"category":"Entry Barrier","values":{"Role A":"Medium","Role B":"Low-Medium"}}],"detailed_analysis":"Paragraph comparing the roles...","recommendation":"Based on the comparison..."}' . "\n"
            . "Provide at least 8 comparison categories with realistic data.";

        $prompt = "Compare these roles/positions in detail: {$roleList}\n"
            . "Include salary ranges, growth potential, required skills, work-life balance, "
            . "market demand, remote opportunities, and career progression.\n"
            . "Reply with valid JSON only.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'comparison' => $result['data']];
        }

        return ['success' => true, 'comparison' => null, 'text' => $result['text'] ?? 'Failed to compare roles'];
    }

    // ─── Saved Searches ──────────────────────────────────────

    /**
     * Save a search to the saved_searches table.
     */
    public function saveSearch(array $searchData): array
    {
        $id         = 'search_' . time() . '_' . bin2hex(random_bytes(4));
        $searchType = $searchData['type']     ?? 'job_search';
        $query      = $searchData['query']    ?? $searchData['roles'][0] ?? 'Unknown';
        $location   = $searchData['location'] ?? null;
        $level      = $searchData['level']    ?? null;

        $sql = "INSERT INTO `saved_searches` (id, search_type, query, location, level, result_data, metadata, created_at, updated_at)
                VALUES (:id, :type, :query, :loc, :lvl, :data, :meta, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'    => $id,
            ':type'  => $searchType,
            ':query' => $query,
            ':loc'   => $location,
            ':lvl'   => $level,
            ':data'  => null,
            ':meta'  => json_encode($searchData, JSON_UNESCAPED_UNICODE),
        ]);

        return ['success' => true, 'id' => $id];
    }

    /**
     * Get all saved search history.
     */
    public function getHistory(): array
    {
        $sql  = "SELECT * FROM `saved_searches` ORDER BY created_at DESC LIMIT 100";
        $stmt = $this->db->query($sql);
        $rows = $stmt->fetchAll();

        // Format for the frontend
        $history = array_map(function ($row) {
            return [
                '_id'      => $row['id'],
                '_type'    => $row['search_type'],
                'query'    => $row['query'],
                'location' => $row['location'],
                'level'    => $row['level'],
                '_updated' => $row['updated_at'],
                '_created' => $row['created_at'],
                'roles'    => $this->extractRoles($row['metadata']),
            ];
        }, $rows);

        return ['success' => true, 'history' => $history];
    }

    /**
     * Delete a saved search.
     */
    public function deleteSearch(string $id): array
    {
        $sql  = "DELETE FROM `saved_searches` WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $deleted = $stmt->rowCount() > 0;
        return ['success' => $deleted, 'message' => $deleted ? 'Deleted' : 'Not found'];
    }

    // ─── MySQL Helpers ───────────────────────────────────────

    /**
     * Save an AI result to the research_results table.
     */
    private function saveResult(string $query, string $resultType, string $resultText, ?array $metadata = null): void
    {
        $sql = "INSERT INTO `research_results` (query, result_type, result_text, metadata, created_at)
                VALUES (:query, :type, :text, :meta, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':query' => $query,
            ':type'  => $resultType,
            ':text'  => $resultText,
            ':meta'  => $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }

    /**
     * Extract roles array from metadata JSON string.
     */
    private function extractRoles(?string $metadataJson): ?array
    {
        if (empty($metadataJson)) return null;
        $meta = json_decode($metadataJson, true);
        return $meta['roles'] ?? null;
    }
}
