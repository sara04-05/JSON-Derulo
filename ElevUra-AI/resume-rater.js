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
        }, 1500);
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

function performAIAnalysis(text) {
    const feedback = [];
    const lines = text.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
    const wordCount = text.split(/\s+/).filter(Boolean).length;

    // Normalize text to handle weird PDF spacing 
    const normalizedText = text.replace(/\s+/g, ' ');
    // Squish text entirely to catch headers parsed as "W O R K  E X P E R I E N C E" or "S K I L L S"
    const squishedText = text.replace(/\s+/g, '').toLowerCase();

    // 1. Structure & Sections (0-100)
    let structureScore = 100;

    // Use squishedText and remove strict \b boundaries to make it bulletproof against PDF formatting quirks
    const hasSummary = /(summary|objective|profile|aboutme)/i.test(squishedText);
    const hasExperience = /(experience|workhistory|employment|career)/i.test(squishedText);
    const hasEducation = /(education|academic|qualifications|degree)/i.test(squishedText);
    const hasSkills = /(skills|technologies|tools|competencies)/i.test(squishedText);

    // More forgiving contact regex for different phone and email formats
    const hasContact = /[\w\.-]+@[\w\.-]+\.\w{2,4}/.test(normalizedText) || /\+?[\d\s.-]{8,15}/.test(normalizedText);

    if (!hasSummary) { structureScore -= 15; feedback.push({ type: 'improvement', title: 'Add a Professional Summary', text: 'Include a 2-3 sentence overview highlighting your key value proposition.' }); }
    if (!hasExperience) { structureScore -= 30; feedback.push({ type: 'warning', title: 'Missing Experience Section', text: 'Clearly label your work history section. This is critical for ATS parsing.' }); }
    if (!hasEducation) { structureScore -= 15; feedback.push({ type: 'warning', title: 'Missing Education', text: 'Ensure your education section is clearly labeled and contains your degree(s).' }); }
    if (!hasSkills) { structureScore -= 15; feedback.push({ type: 'improvement', title: 'Add a Dedicated Skills Section', text: 'Listing skills clearly helps ATS match you to job requirements.' }); }
    if (!hasContact) { structureScore -= 25; feedback.push({ type: 'warning', title: 'Missing Contact Info', text: 'Make sure your email address and phone number are easy to find.' }); }

    // 2. Impact & Metrics (0-100)
    let impactScore = 40;
    const numberMatches = (normalizedText.match(/\b\d+(\.\d+)?[%kmb]?\b/gi) || []).length;
    const metricWords = /(increased|decreased|improved|reduced|saved|generated|revenue|budget)/gi;
    const metricMatches = (normalizedText.match(metricWords) || []).length;

    impactScore += Math.min(30, numberMatches * 3);
    impactScore += Math.min(30, metricMatches * 5);

    if (numberMatches < 3) {
        feedback.push({ type: 'improvement', title: 'Quantify Your Impact', text: 'Use more numbers, percentages, and dollar amounts to show the scale of your achievements.' });
    } else if (numberMatches > 8) {
        feedback.push({ type: 'strength', title: 'Strong Use of Metrics', text: 'You effectively quantify your achievements, giving recruiters clear context on your impact.' });
    }

    // 3. Action Language (0-100)
    let actionScore = 40;
    const weakWords = /(helped|assisted|worked on|responsible for|duties included)/gi;
    // Added 'built' as a strong verb which is common for SWEs
    const strongVerbs = /(spearheaded|architected|orchestrated|delivered|engineered|transformed|optimized|pioneered|mentored|led|managed|developed|designed|implemented|created|built)/gi;

    const weakMatches = (normalizedText.match(weakWords) || []).length;
    const strongMatches = (normalizedText.match(strongVerbs) || []).length;

    actionScore += Math.min(60, strongMatches * 4);
    actionScore -= (weakMatches * 5);
    actionScore = Math.max(0, actionScore);

    if (weakMatches > 2) {
        feedback.push({ type: 'warning', title: 'Remove Passive Language', text: 'Replace weak phrases like "responsible for" or "helped with" with strong action verbs (e.g., "Led", "Engineered").' });
    }
    if (strongMatches > 5) {
        feedback.push({ type: 'strength', title: 'Powerful Action Verbs', text: 'You use strong, active language to describe your responsibilities.' });
    }

    // 4. Formatting & Length (0-100)
    let formatScore = 100;

    if (wordCount < 150) {
        formatScore -= 30;
        feedback.push({ type: 'warning', title: 'Resume Too Short', text: 'Your resume lacks detail. Expand on your experiences and achievements.' });
    } else if (wordCount > 800) {
        formatScore -= 20;
        feedback.push({ type: 'improvement', title: 'Resume Too Long', text: 'Consider condensing your resume to highlight only the most relevant, recent experience.' });
    }

    // Improved bullet point detection using standard unicode bullets (PDFs rarely parse newline hyphens well)
    const bulletPoints = (text.match(/[•*-\u2022\u2023\u25E6\u2043]/g) || []).length;
    if (bulletPoints < 3 && wordCount > 200) {
        formatScore -= 20;
        feedback.push({ type: 'improvement', title: 'Use Bullet Points', text: 'Break dense paragraphs into bullet points for better readability.' });
    } else if (bulletPoints >= 3) {
        feedback.push({ type: 'strength', title: 'Good Readability', text: 'Excellent use of bullet points makes your experience easy to scan.' });
    }

    // Ensure all scores are bounded
    structureScore = Math.max(0, Math.min(100, structureScore));
    impactScore = Math.max(0, Math.min(100, impactScore));
    actionScore = Math.max(0, Math.min(100, actionScore));
    formatScore = Math.max(0, Math.min(100, formatScore));

    const categories = [
        { id: 'structure', label: 'Structure & Sections', score: structureScore },
        { id: 'impact', label: 'Quantifiable Impact', score: impactScore },
        { id: 'language', label: 'Action-Oriented Language', score: actionScore },
        { id: 'formatting', label: 'Length & Formatting', score: formatScore }
    ];

    const rawAverage = (structureScore + impactScore + actionScore + formatScore) / 4;
    let score = Math.round(rawAverage);

    if (score >= 85) {
        feedback.unshift({ type: 'strength', title: 'Outstanding Resume', text: 'Your resume is highly optimized for both ATS and human recruiters. Excellent work.' });
    }

    // Deduplicate feedback titles
    const uniqueFeedback = [];
    const titles = new Set();
    for (const f of feedback) {
        if (!titles.has(f.title)) {
            uniqueFeedback.push(f);
            titles.add(f.title);
        }
    }

    return {
        score,
        categories,
        categoryAverage: Math.round(rawAverage * 10) / 10,
        tier: getTier(score),
        overallAssessment: getAssessment(score),
        feedback: uniqueFeedback
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
    if (score >= 85) {
        return 'Your resume is exceptional! It showcases your experience effectively with strong action verbs, quantifiable results, and proper structure. You\'re well-positioned for top opportunities.';
    } else if (score >= 70) {
        return 'Your resume is strong and well-crafted. Review the suggestions below to refine your impact metrics and ensure ATS compatibility to push it to excellence.';
    } else if (score >= 50) {
        return 'Your resume has a solid foundation but could use some targeted improvements. Focus on quantifying your achievements and removing passive language.';
    } else {
        return 'Your resume needs substantial revision. Focus on basic structure, expanding on your impact with numbers, and replacing weak phrasing with strong action verbs.';
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
                <div class="feedback-label">Action Required</div>
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
                <div class="feedback-title" style="margin-bottom: 24px;">💡 Strengths & Suggestions</div>
                <div>${suggestionsHTML || '<div style="color: var(--text-secondary);">No suggestions at this time.</div>'}</div>
            </div>

            <div class="rating-card">
                <div class="feedback-title" style="margin-bottom: 24px;">⚠️ Areas to Address</div>
                <div>${warningsHTML || '<div style="color: var(--text-secondary);">Great job! No critical areas to address.</div>'}</div>
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
