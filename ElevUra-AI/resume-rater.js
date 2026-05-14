let selectedFile = null;

// Drag and drop handling
const uploadArea = document.getElementById('uploadArea');
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('drag-over');
});

uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('drag-over');
});

uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        handleFileSelection(files[0]);
    }
});

// File input handling
document.getElementById('fileInput').addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelection(e.target.files[0]);
    }
});

function handleFileSelection(file) {
    const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
    
    if (!validTypes.includes(file.type)) {
        alert('Please upload a valid resume file (PDF, DOC, DOCX, or TXT)');
        return;
    }

    selectedFile = file;
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(2) + ' KB';
    document.getElementById('fileInfo').classList.add('show');
    document.getElementById('analyzeButton').disabled = false;
}

function clearFile() {
    selectedFile = null;
    document.getElementById('fileInput').value = '';
    document.getElementById('fileInfo').classList.remove('show');
    document.getElementById('analyzeButton').disabled = true;
    document.getElementById('resultsSection').classList.remove('show');
}

async function analyzeResume() {
    if (!selectedFile) {
        alert('Please select a file first');
        return;
    }

    const uploadSection = document.querySelector('.upload-section');
    const resultsSection = document.getElementById('resultsSection');
    
    uploadSection.style.display = 'none';
    resultsSection.classList.add('show');
    resultsSection.innerHTML = `
        <div class="rating-card">
            <div class="loading">
                <div class="spinner"></div>
                <div class="loading-text">Analyzing your resume with AI...</div>
            </div>
        </div>
    `;

    // Simulate AI analysis - In production, this would call your backend API
    try {
        const resumeText = await readFileAsText(selectedFile);
        const analysisResult = performAIAnalysis(resumeText);
        
        setTimeout(() => {
            displayResults(analysisResult);
        }, 2000);
    } catch (error) {
        console.error('Error reading file:', error);
        resultsSection.innerHTML = `
            <div class="rating-card" style="text-align: center; padding: 40px;">
                <div style="font-size: 18px; color: #ef4444;">Error analyzing resume</div>
                <div style="font-size: 14px; color: var(--text-secondary); margin-top: 12px;">Please try again with a different file</div>
            </div>
        `;
    }
}

function readFileAsText(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve(e.target.result);
        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsText(file);
    });
}

/**
 * Heuristic scan-ability: bullets, multiple lines, and line length (not section titles).
 */
function scorePresentationAndScanability(rawText) {
    const text = (rawText || '').trim();
    if (!text) return 0;

    const lines = text.split(/\r?\n/).map((l) => l.trim()).filter(Boolean);
    const lineCount = lines.length;
    const bulletLineRe = /^\s*([•▪▸·◦]|\d{1,2}[.)]|[\-*])\s+/;
    const bulletMatches = lines.filter((l) => bulletLineRe.test(l) || /^[\t ]{0,3}[-–—][\t ]+\S/.test(l)).length;
    const bulletRatio = lineCount ? bulletMatches / lineCount : 0;

    let lineScore = 12;
    if (lineCount >= 14) lineScore = 35;
    else if (lineCount >= 10) lineScore = 32;
    else if (lineCount >= 7) lineScore = 26;
    else if (lineCount >= 5) lineScore = 20;
    else if (lineCount >= 3) lineScore = 14;

    let bulletScore = 8;
    if (bulletRatio >= 0.38) bulletScore = 35;
    else if (bulletRatio >= 0.22) bulletScore = 30;
    else if (bulletRatio >= 0.1) bulletScore = 22;
    else if (bulletMatches >= 2) bulletScore = 16;

    const avgWords =
        lineCount > 0
            ? lines.reduce((sum, l) => sum + l.split(/\s+/).filter(Boolean).length, 0) / lineCount
            : 0;
    let densityScore = 12;
    if (avgWords >= 7 && avgWords <= 22) densityScore = 30;
    else if (avgWords > 22 && avgWords <= 34) densityScore = 20;
    else if (avgWords > 34) densityScore = 6;
    else if (avgWords >= 4) densityScore = 22;

    return Math.min(100, Math.round(lineScore + bulletScore + densityScore));
}

/** Section hints for feedback only (not a scored category). */
function resumeSectionHints(rawText) {
    const text = rawText || '';

    const hasEmail = /\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/.test(text);
    const hasPhone =
        /(\+\d{1,3}[-.\s])?(\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}|\d{3}[-.\s]\d{3}[-.\s]\d{4})/.test(text);
    const hasLinkedIn = /linkedin\.com\/in\//i.test(text);
    const hasContact = hasEmail || hasPhone || hasLinkedIn;

    const hasSummary =
        /\b(professional\s+summary|executive\s+summary|resume\s+summary|summary\s+of\s+qualifications|career\s+objective|about\s+me|key\s+qualifications|highlights)\b/i.test(
            text
        ) ||
        /(?:^|[\r\n])\s*(summary|objective)\s*[:\-–]\s+/im.test(text) ||
        /(?:^|[\r\n])\s{0,4}(summary|objective)\s*$/im.test(text);

    const hasExpSection =
        /\b(work\s+experience|professional\s+experience|employment(\s+history)?|relevant\s+experience|career(\s+history)?|positions?\s+held)\b/i.test(text) ||
        /(?:^|[\r\n])\s*experience\s*[:\-–]\s+/im.test(text);
    const hasExperienceWord = /\bexperience\b/i.test(text) && /\b(19|20)\d{2}\b/.test(text);
    const hasAltWork =
        /\b(internship|internships|project(\s+experience)?|research(\s+experience)?|volunteer(\s+work)?)\b/i.test(text);
    const yearMatches = text.match(/\b(19|20)\d{2}\b/g) || [];
    const hasJobTimeline =
        new Set(yearMatches).size >= 2 &&
        /\b(engineer|developer|manager|analyst|consultant|lead|director|specialist|coordinator|assistant|designer|architect|scientist|associate|intern|supervisor|representative)\b/i.test(
            text
        );

    let experiencePts = 0;
    if (hasExpSection) experiencePts = 36;
    else if (hasExperienceWord) experiencePts = 28;
    else if (hasAltWork && hasJobTimeline) experiencePts = 26;
    else if (hasJobTimeline) experiencePts = 18;
    else if (hasAltWork) experiencePts = 12;
    const hasExperience = experiencePts >= 18;

    const hasEduSection = /\b(education|academic(\s+background)?|qualifications?)\b/i.test(text);
    const hasEduContent =
        /\b(university|college|institute|bachelor|master|mba|ph\.?\s*d\.?|associate|diploma|certification|coursework)\b/i.test(text);
    const hasEducation = hasEduSection || hasEduContent;

    const hasSkills = /\b(skills?|technical\s+skills|core\s+competencies|technology\s+stack|proficiencies)\b/i.test(text);

    return { hasSummary, hasExperience, hasEducation, hasSkills, hasContact };
}

function performAIAnalysis(text) {
    const feedback = [];

    const presentationScore = scorePresentationAndScanability(text);
    const { hasSummary, hasExperience, hasEducation, hasSkills, hasContact } = resumeSectionHints(text);

    if (presentationScore < 45) {
        feedback.push({
            type: 'improvement',
            title: 'Improve Scan-ability',
            text: 'Break up dense paragraphs, add clear line breaks, and use bullet points so recruiters can skim your accomplishments quickly.'
        });
    } else if (presentationScore >= 78) {
        feedback.push({
            type: 'strength',
            title: 'Easy to Skim',
            text: 'Your text is split into readable lines with bullets or short blocks—good for a quick first pass.'
        });
    }

    if (!hasSummary) {
        feedback.push({ type: 'improvement', title: 'Add Professional Summary', text: 'Include a brief professional summary at the top of your resume to highlight your value proposition.' });
    }
    if (!hasExperience) {
        feedback.push({ type: 'warning', title: 'Experience Section Missing', text: 'Add detailed work experience with accomplishments and dates (use a clear header such as Work experience or Professional experience).' });
    }
    if (!hasEducation) {
        feedback.push({ type: 'improvement', title: 'Education Section Needed', text: 'Include your educational background and certifications.' });
    }
    if (!hasSkills) {
        feedback.push({ type: 'warning', title: 'Skills Section Missing', text: 'Add a dedicated skills section to highlight your technical abilities.' });
    }
    if (!hasContact) {
        feedback.push({ type: 'improvement', title: 'Add Contact Details', text: 'Include at least one clear way to reach you (email, phone, or LinkedIn URL).' });
    }

    const wordCount = text.trim() ? text.split(/\s+/).filter(Boolean).length : 0;
    let contentScore = 50;
    if (wordCount < 50) {
        contentScore = 25;
        feedback.push({ type: 'warning', title: 'Content Too Short', text: 'Expand your resume with more detailed information about your background.' });
    } else if (wordCount < 100) {
        contentScore = 45;
        feedback.push({ type: 'warning', title: 'Content Too Short', text: 'Expand your resume with more detailed information about your background.' });
    } else if (wordCount <= 550) {
        contentScore = 92;
    } else if (wordCount <= 900) {
        contentScore = 88;
    } else if (wordCount <= 1000) {
        contentScore = 78;
    } else {
        contentScore = 62;
        feedback.push({ type: 'improvement', title: 'Consider Condensing', text: 'Your resume might be too lengthy. Aim for 1-2 pages maximum.' });
    }

    const actionVerbs = /led|managed|developed|designed|implemented|created|improved|increased|reduced|achieved/i;
    const hasStrongVerbs = actionVerbs.test(text);
    const languageScore = hasStrongVerbs ? 88 : 42;
    if (hasStrongVerbs) {
        feedback.push({ type: 'strength', title: 'Strong Action Verbs', text: 'Great use of powerful action verbs to describe achievements.' });
    } else {
        feedback.push({ type: 'improvement', title: 'Use Action Verbs', text: 'Replace passive language with action verbs like "Led", "Managed", "Developed".' });
    }

    const hasMetrics = /\d+%|\$\d+|increased|reduced|saved|earned/i.test(text);
    const impactScore = hasMetrics ? 90 : 38;
    if (hasMetrics) {
        feedback.push({ type: 'strength', title: 'Quantifiable Results', text: 'Excellent use of metrics and numbers to demonstrate impact.' });
    } else {
        feedback.push({ type: 'improvement', title: 'Add Quantifiable Metrics', text: 'Include numbers, percentages, or dollar amounts to show concrete impact.' });
    }

    const categories = [
        { id: 'presentation', label: 'Presentation & scan-ability', score: presentationScore },
        { id: 'content', label: 'Content depth & length', score: contentScore },
        { id: 'language', label: 'Action-oriented language', score: languageScore },
        { id: 'impact', label: 'Measurable impact', score: impactScore }
    ];

    const rawAverage = categories.reduce((sum, c) => sum + c.score, 0) / categories.length;
    let score = Math.round(rawAverage);
    score = Math.max(0, Math.min(100, score));

    if (score >= 75) {
        feedback.unshift({ type: 'strength', title: 'Strong overall signal', text: 'Your resume reads clearly across length, wording, impact, and layout cues—keep tailoring it to each role.' });
    }

    return {
        score,
        categories,
        categoryAverage: Math.round(rawAverage * 10) / 10,
        tier: getTier(score),
        overallAssessment: getAssessment(score),
        feedback
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

function getAssessment(score) {
    if (score >= 90) {
        return 'Your resume is exceptional! It showcases your experience effectively with strong action verbs, quantifiable results, and proper structure. You\'re well-positioned for top opportunities.';
    } else if (score >= 80) {
        return 'Your resume is strong and well-crafted. Consider adding more quantifiable metrics and ensuring all key sections are clearly defined to push it to excellence.';
    } else if (score >= 70) {
        return 'Your resume has a solid foundation but could use some improvements. Focus on adding metrics, stronger action verbs, and ensuring all relevant sections are included.';
    } else if (score >= 60) {
        return 'Your resume has potential but needs significant improvements. Add missing sections, use more powerful language, and include specific examples of your achievements.';
    } else {
        return 'Your resume needs substantial revision. Make sure to include all key sections (summary, experience, education, skills) and expand on your accomplishments with concrete examples.';
    }
}

function displayResults(result) {
    const resultsSection = document.getElementById('resultsSection');
    
    // Separate feedback into categories
    const suggestions = result.feedback.filter(item => item.type === 'improvement');
    const warnings = result.feedback.filter(item => item.type === 'warning');
    const strengths = result.feedback.filter(item => item.type === 'strength');
    
    // Create suggestions HTML
    let suggestionsHTML = [...strengths, ...suggestions].map(item => `
        <div class="feedback-item ${item.type}">
            <div class="feedback-icon">
                ${item.type === 'strength' ? '✅' : '💡'}
            </div>
            <div class="feedback-content">
                <div class="feedback-label">${item.type === 'strength' ? 'Strength' : 'Suggestion'}</div>
                <div class="feedback-text"><strong>${item.title}</strong></div>
                <div class="feedback-text">${item.text}</div>
            </div>
        </div>
    `).join('');

    // Create warnings HTML
    let warningsHTML = warnings.map(item => `
        <div class="feedback-item ${item.type}">
            <div class="feedback-icon">⚠️</div>
            <div class="feedback-content">
                <div class="feedback-label">Warning</div>
                <div class="feedback-text"><strong>${item.title}</strong></div>
                <div class="feedback-text">${item.text}</div>
            </div>
        </div>
    `).join('');

    const categories = result.categories || [];
    const breakdownHTML = categories.length
        ? `
        <div class="category-breakdown">
            <div class="category-breakdown-title">Score breakdown</div>
            ${categories.map((c) => `
                <div class="category-row" data-category-score="${c.score}">
                    <div class="category-row-meta">
                        <div class="category-row-label">${c.label}</div>
                        <div class="category-bar-track" aria-hidden="true">
                            <div class="category-bar-fill" data-bar-fill style="width: 0%"></div>
                        </div>
                    </div>
                    <div class="category-row-score">${c.score}</div>
                </div>
            `).join('')}
            <p class="category-average-note">
                Overall score <strong>${result.score}</strong> is the rounded average of these categories
                ${typeof result.categoryAverage === 'number' ? `(mean <strong>${result.categoryAverage}</strong>)` : ''}.
            </p>
        </div>
    `
        : '';

    resultsSection.innerHTML = `
        <div class="rating-card rating-card--score">
            <div class="rating-container">
                <div class="rating-circle-container">
                    <div class="rating-circle" id="ratingCircle" style="--score-fill: 0;">
                        <div class="rating-inner">
                            <div class="rating-score" id="ratingScore">0</div>
                            <div class="rating-label">Score</div>
                        </div>
                    </div>
                    <div class="rating-tier">${result.tier}</div>
                </div>

                <div class="feedback-section">
                    <div>
                        <div class="feedback-title">Overall Assessment</div>
                        <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.8;">
                            ${result.overallAssessment}
                        </div>
                        ${breakdownHTML}
                    </div>
                </div>
            </div>
        </div>

        <div class="feedback-columns">
            <div class="rating-card">
                <div class="feedback-title" style="margin-bottom: 24px;">💡 Suggestions & Improvements</div>
                <div>${suggestionsHTML || '<div style="color: var(--text-secondary);">No suggestions at this time.</div>'}</div>
            </div>

            <div class="rating-card">
                <div class="feedback-title" style="margin-bottom: 24px;">⚠️ Areas to Address</div>
                <div>${warningsHTML || '<div style="color: var(--text-secondary);">Great job! No critical warnings.</div>'}</div>
            </div>
        </div>
    `;

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            animateFinalScore(result.score);
            document.querySelectorAll('[data-bar-fill]').forEach((bar) => {
                const row = bar.closest('.category-row');
                const target = row ? parseInt(row.getAttribute('data-category-score'), 10) : 0;
                requestAnimationFrame(() => {
                    bar.style.width = `${Math.max(0, Math.min(100, target))}%`;
                });
            });
        });
    });
}

function animateFinalScore(targetScore) {
    const circle = document.getElementById('ratingCircle');
    const scoreEl = document.getElementById('ratingScore');
    if (!circle || !scoreEl) return;

    scoreEl.classList.remove('score-count-done');
    circle.classList.remove('ring-complete');

    const duration = Math.min(2000, 750 + targetScore * 10);
    const start = performance.now();

    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    function tick(now) {
        const t = Math.min(1, (now - start) / duration);
        const eased = easeOutCubic(t);
        const display = Math.round(eased * targetScore);
        scoreEl.textContent = String(display);
        circle.style.setProperty('--score-fill', String(eased * targetScore));
        if (t < 1) {
            requestAnimationFrame(tick);
        } else {
            scoreEl.textContent = String(targetScore);
            circle.style.setProperty('--score-fill', String(targetScore));
            scoreEl.classList.add('score-count-done');
            circle.classList.add('ring-complete');
        }
    }

    requestAnimationFrame(tick);
}
