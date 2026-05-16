/**
 * ATS resume analysis engine: normalization, scoring, skills, keywords, feedback
 */
(function (global) {
    'use strict';

    const SKILLS_DB = {
        languages: [
            'javascript', 'typescript', 'python', 'java', 'c++', 'c#', 'go', 'golang', 'rust',
            'ruby', 'php', 'swift', 'kotlin', 'scala', 'r', 'matlab', 'sql', 'html', 'css'
        ],
        frameworks: [
            'react', 'angular', 'vue', 'next.js', 'node.js', 'express', 'django', 'flask',
            'spring', 'spring boot', '.net', 'laravel', 'rails', 'fastapi', 'tensorflow',
            'pytorch', 'pandas', 'numpy'
        ],
        tools: [
            'git', 'docker', 'kubernetes', 'jenkins', 'github actions', 'jira', 'confluence',
            'figma', 'postman', 'terraform', 'ansible', 'linux', 'bash', 'webpack', 'vite'
        ],
        databases: [
            'mysql', 'postgresql', 'mongodb', 'redis', 'elasticsearch', 'dynamodb', 'sqlite',
            'oracle', 'sql server', 'cassandra', 'firebase'
        ],
        cloud: [
            'aws', 'azure', 'gcp', 'google cloud', 'ec2', 's3', 'lambda', 'cloudformation',
            'heroku', 'vercel', 'netlify'
        ],
        soft: [
            'leadership', 'communication', 'teamwork', 'problem solving', 'critical thinking',
            'project management', 'agile', 'scrum', 'mentoring', 'collaboration', 'negotiation',
            'time management', 'stakeholder management'
        ]
    };

    const STOP_WORDS = new Set([
        'the', 'and', 'for', 'with', 'this', 'that', 'from', 'your', 'you', 'will', 'our',
        'are', 'was', 'were', 'have', 'has', 'had', 'been', 'being', 'their', 'they', 'them',
        'who', 'which', 'what', 'when', 'where', 'while', 'would', 'should', 'could', 'may',
        'might', 'must', 'can', 'able', 'about', 'into', 'through', 'during', 'before', 'after',
        'above', 'below', 'between', 'under', 'again', 'further', 'then', 'once', 'here', 'there',
        'all', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'only', 'own', 'same',
        'than', 'too', 'very', 'just', 'also', 'now', 'job', 'role', 'position', 'work', 'team',
        'company', 'experience', 'required', 'preferred', 'including', 'using', 'use', 'used'
    ]);

    const STRONG_VERBS = [
        'led', 'managed', 'developed', 'designed', 'implemented', 'created', 'built', 'architected',
        'spearheaded', 'orchestrated', 'delivered', 'engineered', 'transformed', 'optimized',
        'pioneered', 'mentored', 'increased', 'reduced', 'improved', 'achieved', 'launched',
        'streamlined', 'automated', 'negotiated', 'analyzed', 'established'
    ];

    const WEAK_PHRASES = [
        'responsible for', 'duties included', 'helped with', 'assisted with', 'worked on',
        'involved in', 'participated in', 'was tasked with'
    ];

    const VAGUE_WORDS = [
        'various', 'several', 'many', 'some', 'etc', 'stuff', 'things', 'nice', 'good',
        'great', 'excellent', 'varied'
    ];

    /** Safe normalization — no squished text */
    function normalizeResumeText(raw) {
        let t = raw || '';
        t = t.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
        t = t.replace(/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/g, '');
        t = t.replace(/\u00A0/g, ' ');
        t = t.replace(/[ \t]+/g, ' ');
        t = t.replace(/\n{3,}/g, '\n\n');
        return t.trim();
    }

    function toSearchText(text) {
        return normalizeResumeText(text)
            .toLowerCase()
            .replace(/[^\w\s+#.@/-]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function hasPhrase(searchText, pattern) {
        if (pattern instanceof RegExp) return pattern.test(searchText);
        const escaped = pattern.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return new RegExp('\\b' + escaped + '\\b', 'i').test(searchText);
    }

    function resumeSectionHints(text, searchText) {
        const hasEmail = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/.test(text);
        const hasPhone = /(\+\d{1,3}[-.\s])?(\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4})/.test(text);
        const hasLinkedIn = /linkedin\.com/i.test(text);
        const hasContact = hasEmail || hasPhone || hasLinkedIn;

        const hasSummary =
            hasPhrase(searchText, 'professional summary') ||
            hasPhrase(searchText, 'executive summary') ||
            hasPhrase(searchText, 'career objective') ||
            hasPhrase(searchText, 'summary of qualifications') ||
            /(?:^|\n)\s*(summary|objective)\s*[:\-–]?\s*\n/im.test(text);

        const hasExperience =
            hasPhrase(searchText, 'work experience') ||
            hasPhrase(searchText, 'professional experience') ||
            hasPhrase(searchText, 'employment history') ||
            hasPhrase(searchText, 'relevant experience') ||
            (hasPhrase(searchText, 'experience') && /\b(19|20)\d{2}\b/.test(text));

        const hasEducation =
            hasPhrase(searchText, 'education') ||
            hasPhrase(searchText, 'bachelor') ||
            hasPhrase(searchText, 'master') ||
            hasPhrase(searchText, 'university') ||
            hasPhrase(searchText, 'degree');

        const hasSkills =
            hasPhrase(searchText, 'skills') ||
            hasPhrase(searchText, 'technical skills') ||
            hasPhrase(searchText, 'core competencies') ||
            hasPhrase(searchText, 'technologies');

        return { hasSummary, hasExperience, hasEducation, hasSkills, hasContact };
    }

    function scorePresentation(text) {
        const lines = text.split(/\n/).map((l) => l.trim()).filter(Boolean);
        const lineCount = lines.length;
        const bulletRe = /^\s*([•▪▸·◦]|\d{1,2}[.)]|[\-*])\s+/;
        const bullets = lines.filter((l) => bulletRe.test(l)).length;
        const ratio = lineCount ? bullets / lineCount : 0;

        let score = 40;
        if (lineCount >= 12) score += 20;
        else if (lineCount >= 6) score += 12;
        if (ratio >= 0.25) score += 25;
        else if (ratio >= 0.1) score += 15;
        else if (bullets >= 2) score += 8;

        const avgWords =
            lineCount > 0
                ? lines.reduce((s, l) => s + l.split(/\s+/).filter(Boolean).length, 0) / lineCount
                : 0;
        if (avgWords >= 6 && avgWords <= 22) score += 15;
        else if (avgWords > 30) score -= 10;

        return Math.max(0, Math.min(100, score));
    }

    function extractSkills(searchText) {
        const found = { languages: [], frameworks: [], tools: [], databases: [], cloud: [], soft: [] };
        for (const [cat, list] of Object.entries(SKILLS_DB)) {
            for (const skill of list) {
                if (hasPhrase(searchText, skill) && !found[cat].includes(skill)) {
                    found[cat].push(skill);
                }
            }
        }
        const all = Object.values(found).flat();
        return { categorized: found, all, count: all.length };
    }

    function extractJobKeywords(jobDescription) {
        if (!jobDescription || !jobDescription.trim()) return [];
        const search = toSearchText(jobDescription);
        const tokens = search.split(/\s+/).filter((w) => w.length > 2 && !STOP_WORDS.has(w));
        const bigrams = [];
        for (let i = 0; i < tokens.length - 1; i++) {
            bigrams.push(tokens[i] + ' ' + tokens[i + 1]);
        }
        const freq = {};
        for (const t of [...tokens, ...bigrams]) {
            freq[t] = (freq[t] || 0) + 1;
        }
        return Object.entries(freq)
            .sort((a, b) => b[1] - a[1])
            .slice(0, 40)
            .map(([word]) => word)
            .filter((w, i, arr) => arr.indexOf(w) === i);
    }

    function matchKeywords(resumeSearch, keywords) {
        if (!keywords.length) {
            return { score: null, matched: [], missing: [], keywords: [] };
        }
        const matched = [];
        const missing = [];
        for (const kw of keywords) {
            if (hasPhrase(resumeSearch, kw) || resumeSearch.includes(kw)) {
                matched.push(kw);
            } else {
                missing.push(kw);
            }
        }
        const score = Math.round((matched.length / keywords.length) * 100);
        return { score, matched, missing, keywords };
    }

    function checkATSCompatibility(text, rawText) {
        const warnings = [];
        let score = 100;

        if (/<table[\s>]/i.test(rawText) || /\btable\b.*\bcell\b/i.test(rawText)) {
            warnings.push({
                type: 'warning',
                title: 'Tables Detected',
                text: 'Complex layouts with tables may fail ATS parsing. Use simple single-column formatting when possible.'
            });
            score -= 15;
        }

        if (/\b(column|multicolumn|sidebar)\b/i.test(toSearchText(rawText))) {
            warnings.push({
                type: 'warning',
                title: 'Multi-Column Layout Risk',
                text: 'Multiple columns can scramble text order in ATS systems. Prefer a single-column layout.'
            });
            score -= 12;
        }

        const symbolDensity = (rawText.match(/[│┃┌┐└┘╔╗╚╝■□▪▸►◆★☆]/g) || []).length;
        if (symbolDensity > 8) {
            warnings.push({
                type: 'warning',
                title: 'Heavy Symbol Usage',
                text: 'Excessive decorative symbols may confuse ATS parsers. Stick to standard bullets (• or -).'
            });
            score -= 10;
        }

        if (/\b(photograph|headshot|graphic|chart|image)\b/i.test(toSearchText(rawText))) {
            warnings.push({
                type: 'improvement',
                title: 'Graphics & Images',
                text: 'Graphics and images are invisible to most ATS. Put critical info in plain text.'
            });
            score -= 8;
        }

        const printableRatio =
            rawText.length > 0
                ? (rawText.replace(/[\s\w.,;:'"!?@#$/\-+()&%]/g, '').length / rawText.length)
                : 0;
        if (printableRatio > 0.35) {
            warnings.push({
                type: 'warning',
                title: 'Unusual Characters',
                text: 'Your file contains many non-standard characters. ATS may misread sections.'
            });
            score -= 15;
        }

        return { score: Math.max(0, score), warnings };
    }

    function buildAdvancedFeedback(text, searchText, sections, skills) {
        const feedback = [];
        const lines = text.split(/\n/).map((l) => l.trim()).filter(Boolean);

        for (const phrase of WEAK_PHRASES) {
            if (hasPhrase(searchText, phrase)) {
                feedback.push({
                    type: 'improvement',
                    title: 'Replace Passive Phrasing',
                    text: `Avoid "${phrase}" — lead with a strong verb and your specific contribution.`
                });
                break;
            }
        }

        let vagueCount = 0;
        for (const w of VAGUE_WORDS) {
            if (hasPhrase(searchText, w)) vagueCount++;
        }
        if (vagueCount >= 2) {
            feedback.push({
                type: 'improvement',
                title: 'Reduce Vague Language',
                text: 'Replace words like "various" or "several" with specific numbers, tools, or outcomes.'
            });
        }

        const shortBullets = lines.filter((l) => /^[•*\-–]\s/.test(l) || /^\d+[.)]\s/.test(l)).filter(
            (l) => l.split(/\s+/).length < 6
        );
        if (shortBullets.length >= 2) {
            feedback.push({
                type: 'improvement',
                title: 'Strengthen Bullet Points',
                text: 'Some bullets are too short. Use: Action + Task + Result (ideally with a metric).'
            });
        }

        const strongCount = STRONG_VERBS.filter((v) => hasPhrase(searchText, v)).length;
        if (strongCount >= 6) {
            feedback.push({
                type: 'strength',
                title: 'Strong Action Verbs',
                text: 'You use powerful action verbs that help recruiters scan impact quickly.'
            });
        } else if (strongCount < 2) {
            feedback.push({
                type: 'improvement',
                title: 'Add Action Verbs',
                text: 'Start bullets with verbs like Led, Built, Delivered, Optimized, or Implemented.'
            });
        }

        if (!/\d/.test(text)) {
            feedback.push({
                type: 'improvement',
                title: 'Quantify Achievements',
                text: 'Add metrics (%, $, time saved, team size) so recruiters see measurable impact.'
            });
        } else {
            feedback.push({
                type: 'strength',
                title: 'Quantified Results',
                text: 'Your resume includes numbers that help demonstrate impact.'
            });
        }

        if (skills.count >= 5) {
            feedback.push({
                type: 'strength',
                title: 'Skills Detected',
                text: `Identified ${skills.count} relevant skills — ensure they align with your target role.`
            });
        }

        if (!sections.hasSummary) {
            feedback.push({
                type: 'improvement',
                title: 'Add Professional Summary',
                text: 'A 2–3 line summary at the top improves ATS keyword density and recruiter first impressions.'
            });
        }

        return feedback;
    }

    function buildChecklist(sections, keywordResult, atsCompat) {
        const items = [];
        items.push({ done: sections.hasContact, text: 'Contact information visible' });
        items.push({ done: sections.hasSummary, text: 'Professional summary included' });
        items.push({ done: sections.hasExperience, text: 'Work experience section present' });
        items.push({ done: sections.hasEducation, text: 'Education section present' });
        items.push({ done: sections.hasSkills, text: 'Dedicated skills section' });
        if (keywordResult.score !== null) {
            items.push({
                done: keywordResult.score >= 60,
                text: 'Keyword match ≥ 60% for target job'
            });
        }
        items.push({ done: atsCompat.score >= 75, text: 'ATS-friendly formatting' });
        return items;
    }

    function clamp(n) {
        return Math.max(0, Math.min(100, Math.round(n)));
    }

    function analyze(rawText, jobDescription) {
        const text = normalizeResumeText(rawText);
        const searchText = toSearchText(text);
        const wordCount = text.split(/\s+/).filter(Boolean).length;
        const sections = resumeSectionHints(text, searchText);
        const skills = extractSkills(searchText);
        const atsCompat = checkATSCompatibility(text, rawText);
        const keywords = extractJobKeywords(jobDescription);
        const keywordResult = matchKeywords(searchText, keywords);

        const feedback = buildAdvancedFeedback(text, searchText, sections, skills);
        feedback.push(...atsCompat.warnings);

        if (!sections.hasExperience) {
            feedback.push({
                type: 'warning',
                title: 'Experience Section Missing',
                text: 'Add a clearly labeled work experience section with dates and accomplishments.'
            });
        }
        if (!sections.hasContact) {
            feedback.push({
                type: 'warning',
                title: 'Missing Contact Details',
                text: 'Include email, phone, or LinkedIn so recruiters can reach you.'
            });
        }

        let structureScore = 100;
        if (!sections.hasSummary) structureScore -= 12;
        if (!sections.hasExperience) structureScore -= 28;
        if (!sections.hasEducation) structureScore -= 12;
        if (!sections.hasSkills) structureScore -= 12;
        if (!sections.hasContact) structureScore -= 18;
        structureScore = clamp(structureScore);

        let impactScore = 35;
        const numbers = (text.match(/\b\d+(\.\d+)?[%kmb]?\b/gi) || []).length;
        const metricWords = (text.match(/\b(increased|decreased|reduced|saved|generated|grew|improved)\b/gi) || [])
            .length;
        impactScore += Math.min(35, numbers * 4);
        impactScore += Math.min(25, metricWords * 5);
        impactScore = clamp(impactScore);

        let languageScore = 38;
        const strongCount = STRONG_VERBS.filter((v) => hasPhrase(searchText, v)).length;
        const weakCount = WEAK_PHRASES.filter((p) => hasPhrase(searchText, p)).length;
        languageScore += Math.min(45, strongCount * 5);
        languageScore -= weakCount * 8;
        languageScore = clamp(languageScore);

        let contentScore = 70;
        if (wordCount < 120) contentScore -= 25;
        else if (wordCount < 200) contentScore -= 12;
        else if (wordCount > 900) contentScore -= 15;
        else if (wordCount >= 250 && wordCount <= 650) contentScore += 15;
        contentScore = clamp(contentScore);

        const presentationScore = scorePresentation(text);
        const readabilityScore = clamp((presentationScore * 0.6 + contentScore * 0.4));
        const atsOptimizationScore = clamp(
            structureScore * 0.45 + atsCompat.score * 0.35 + presentationScore * 0.2
        );

        const keywordScore =
            keywordResult.score !== null ? keywordResult.score : Math.round(structureScore * 0.85);

        const weights = keywordResult.score !== null
            ? { ats: 0.22, content: 0.18, readability: 0.15, keywords: 0.25, impact: 0.12, language: 0.08 }
            : { ats: 0.28, content: 0.22, readability: 0.2, keywords: 0, impact: 0.18, language: 0.12 };

        let overall =
            atsOptimizationScore * weights.ats +
            contentScore * weights.content +
            readabilityScore * weights.readability +
            impactScore * weights.impact +
            languageScore * weights.language;
        if (weights.keywords) overall += keywordScore * weights.keywords;

        const score = clamp(overall);

        const interviewProbability = clamp(
            score * 0.35 + impactScore * 0.25 + languageScore * 0.2 + keywordScore * 0.2
        );
        const atsPassProbability = clamp(
            atsOptimizationScore * 0.4 + keywordScore * 0.35 + atsCompat.score * 0.25
        );

        const categories = [
            { id: 'ats', label: 'ATS Optimization', score: atsOptimizationScore },
            { id: 'content', label: 'Content Quality', score: contentScore },
            { id: 'readability', label: 'Readability', score: readabilityScore },
            { id: 'impact', label: 'Experience Impact', score: impactScore },
            { id: 'language', label: 'Action Language', score: languageScore }
        ];
        if (keywordResult.score !== null) {
            categories.push({
                id: 'keywords',
                label: 'Keyword Alignment',
                score: keywordScore
            });
        }

        const rawAverage =
            categories.reduce((s, c) => s + c.score, 0) / categories.length;

        if (score >= 82) {
            feedback.unshift({
                type: 'strength',
                title: 'Strong ATS Profile',
                text: 'Your resume scores well across structure, readability, and impact signals.'
            });
        }

        const uniqueFeedback = [];
        const seen = new Set();
        for (const f of feedback) {
            if (!seen.has(f.title)) {
                seen.add(f.title);
                uniqueFeedback.push(f);
            }
        }

        const strengths = uniqueFeedback.filter((f) => f.type === 'strength');
        const criticalIssues = uniqueFeedback.filter((f) => f.type === 'warning');

        return {
            score,
            categories,
            categoryAverage: Math.round(rawAverage * 10) / 10,
            tier: getTier(score),
            overallAssessment: getAssessment(score, keywordResult.score),
            feedback: uniqueFeedback,
            metrics: {
                atsMatch: atsOptimizationScore,
                recruiterReadability: readabilityScore,
                keywordMatch: keywordResult.score,
                interviewProbability,
                atsPassProbability,
                resumeStrength: score
            },
            keywords: keywordResult,
            skills,
            atsCompatibility: atsCompat,
            checklist: buildChecklist(sections, keywordResult, atsCompat),
            strengths,
            criticalIssues,
            meta: { wordCount, sections }
        };
    }

    function getTier(score) {
        if (score >= 90) return '🌟 Excellent';
        if (score >= 80) return '✨ Very Good';
        if (score >= 70) return '👍 Good';
        if (score >= 60) return '📈 Fair';
        if (score >= 50) return '⚠️ Needs Work';
        return '🔴 Poor';
    }

    function getAssessment(score, keywordMatch) {
        let base;
        if (score >= 85) {
            base =
                'Your resume is well-optimized for ATS and recruiter review. Fine-tune keywords for each application.';
        } else if (score >= 70) {
            base =
                'Solid foundation. Address the checklist items and tailor keywords to your target job posting.';
        } else if (score >= 50) {
            base =
                'Room to improve. Focus on structure, metrics, and ATS-safe formatting before applying widely.';
        } else {
            base =
                'Significant gaps detected. Rebuild core sections, add quantified bullets, and simplify layout for ATS.';
        }
        if (keywordMatch !== null && keywordMatch < 50) {
            base += ' Keyword match is low — paste the job description and align your language.';
        }
        return base;
    }

    global.ResumeRaterAnalyzer = {
        analyze,
        normalizeResumeText,
        toSearchText,
        extractSkills,
        SKILLS_DB
    };
})(typeof window !== 'undefined' ? window : globalThis);
