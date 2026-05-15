<?php

namespace Leart\JsonDerulo\Services;

class ResearchService
{
    private GeminiService $gemini;
    private StorageService $storage;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        $this->storage = new StorageService('research_sessions');
    }

    /**
     * Search for jobs using AI-generated structured results.
     */
    public function searchJobs(string $query, string $location = '', string $level = '', string $type = ''): array
    {
        $system = "You are a career research assistant with deep knowledge of the job market. "
            . "Generate realistic, helpful, and detailed job search results. "
            . "Return valid JSON only with this exact structure:\n"
            . '{"query_summary":"...","total_results":8,"results":[{"id":1,"title":"...","company":"...","location":"...","type":"Full-time","salary_range":"$80K-$120K","experience_level":"...","description":"2-3 sentence description","requirements":["req1","req2"],"key_skills":["skill1","skill2"],"match_score":85,"posted_ago":"2 days ago"}],"market_insights":{"demand_level":"High","avg_salary":"...","trending_skills":["..."],"market_summary":"..."}}' . "\n"
            . "Generate 6-8 diverse, realistic results. Match scores should be 60-98. "
            . "Companies should be a mix of well-known and realistic smaller companies.";

        $prompt = "Search for jobs matching: \"{$query}\"";
        if (!empty($location)) $prompt .= "\nLocation: {$location}";
        if (!empty($level)) $prompt .= "\nExperience level: {$level}";
        if (!empty($type)) $prompt .= "\nJob type: {$type}";
        $prompt .= "\nProvide detailed, realistic results with accurate market data.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'results' => $result['data']];
        }

        return ['error' => true, 'message' => 'Failed to generate job results'];
    }

    /**
     * Extract and categorize skills from a job description or text.
     */
    public function extractSkills(string $text): array
    {
        $system = "You are an expert at analyzing job descriptions and extracting skills. "
            . "Return valid JSON only with this exact structure:\n"
            . '{"summary":"Brief analysis","technical_skills":[{"name":"Python","proficiency":"Advanced","category":"Programming Language"}],"soft_skills":["Communication","Leadership"],"tools_platforms":["AWS","Docker"],"certifications_mentioned":["AWS Solutions Architect"],"experience_requirements":{"min_years":3,"preferred_years":5,"education":"Bachelor\'s in CS"},"skill_gap_tips":["Consider learning..."]}'
            . "\nBe thorough and categorize every skill found in the text.";

        $prompt = "Analyze the following text and extract all skills, requirements, and qualifications:\n\n"
            . $text;

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'skills' => $result['data']];
        }

        return ['error' => true, 'message' => 'Failed to extract skills'];
    }

    /**
     * Compare multiple roles/positions.
     */
    public function compareRoles(array $roles): array
    {
        $roleList = implode(', ', $roles);

        $system = "You are a career advisor who provides detailed role comparisons. "
            . "Return valid JSON only with this exact structure:\n"
            . '{"roles_compared":["Role A","Role B"],"comparison_table":[{"category":"Average Salary","values":{"Role A":"$95K-$130K","Role B":"$85K-$115K"}},{"category":"Growth Potential","values":{"Role A":"High","Role B":"Very High"}},{"category":"Key Skills","values":{"Role A":"Python, SQL","Role B":"JavaScript, React"}},{"category":"Work-Life Balance","values":{"Role A":"Good","Role B":"Moderate"}},{"category":"Market Demand","values":{"Role A":"High","Role B":"Very High"}},{"category":"Remote Opportunities","values":{"Role A":"Common","Role B":"Very Common"}},{"category":"Entry Barrier","values":{"Role A":"Medium","Role B":"Low-Medium"}}],"detailed_analysis":"Paragraph comparing the roles...","recommendation":"Based on the comparison..."}'
            . "\nProvide at least 8 comparison categories with realistic data.";

        $prompt = "Compare these roles/positions in detail: {$roleList}\n"
            . "Include salary ranges, growth potential, required skills, work-life balance, "
            . "market demand, remote opportunities, and career progression.";

        $result = $this->gemini->generateJson($prompt, $system);

        if (isset($result['error'])) {
            return $result;
        }

        if (isset($result['data'])) {
            return ['success' => true, 'comparison' => $result['data']];
        }

        return ['error' => true, 'message' => 'Failed to compare roles'];
    }

    /**
     * Save a search to history.
     */
    public function saveSearch(array $searchData): array
    {
        $id = 'search_' . time() . '_' . bin2hex(random_bytes(4));
        $searchData['_type'] = $searchData['type'] ?? 'job_search';
        $this->storage->save($id, $searchData);
        return ['success' => true, 'id' => $id];
    }

    /**
     * Get all saved search history.
     */
    public function getHistory(): array
    {
        $items = $this->storage->listAll();
        return ['success' => true, 'history' => $items];
    }

    /**
     * Delete a saved search.
     */
    public function deleteSearch(string $id): array
    {
        $deleted = $this->storage->delete($id);
        return ['success' => $deleted, 'message' => $deleted ? 'Deleted' : 'Not found'];
    }
}
