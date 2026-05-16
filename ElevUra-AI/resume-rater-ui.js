/**
 * ATS results dashboard UI, animations, downloadable report
 */
(function (global) {
    'use strict';

    const T = 'div';
    const S = 'span';
    let lastAnalysisResult = null;

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function el(tag, className, inner) {
        return '<' + tag + (className ? ' class="' + className + '"' : '') + '>' + inner + '</' + tag + '>';
    }

    function renderFeedbackItem(item) {
        const icon = item.type === 'strength' ? '✅' : item.type === 'warning' ? '⚠️' : '💡';
        const label =
            item.type === 'strength'
                ? 'Strength'
                : item.type === 'warning'
                  ? 'Action Required'
                  : 'Suggestion';
        return (
            el(T, 'feedback-item ' + item.type,
                el('div', 'feedback-icon', icon) +
                el(T, 'feedback-content',
                    el(S, 'feedback-label', label) +
                    el(T, 'feedback-text', '<strong>' + escapeHtml(item.title) + '</strong>') +
                    el(T, 'feedback-text', escapeHtml(item.text))
                )
            )
        );
    }

    function metricCard(label, value, sub) {
        const display = value === null || value === undefined ? '—' : value + '%';
        return (
            '<motionless class="ats-metric-card glass-card">' +
            '<motionless class="ats-metric-label">' + escapeHtml(label) + '</motionless>' +
            '<motionless class="ats-metric-value">' + display + '</motionless>' +
            (sub ? '<motionless class="ats-metric-sub">' + escapeHtml(sub) + '</motionless>' : '') +
            '<motionless class="ats-metric-bar"><motionless class="ats-metric-fill" data-fill="' + (value || 0) + '" style="width:0%"></motionless></motionless>' +
            '</motionless>'
        ).replace(/motionless/g, 'div');
    }

    function skillTags(skills) {
        const cats = skills.categorized;
        const labels = {
            languages: 'Languages',
            frameworks: 'Frameworks',
            tools: 'Tools',
            databases: 'Databases',
            cloud: 'Cloud',
            soft: 'Soft skills'
        };
        let html = '<motionless class="skills-grid">';
        for (const [key, list] of Object.entries(cats)) {
            if (!list.length) continue;
            html += '<motionless class="skill-category glass-card">';
            html += '<motionless class="skill-cat-title">' + escapeHtml(labels[key] || key) + '</motionless>';
            html += '<motionless class="skill-tags">';
            for (const s of list) {
                html += '<motionless class="skill-tag">' + escapeHtml(s) + '</motionless>';
            }
            html += '</motionless></motionless>';
        }
        html += '</motionless>';
        if (skills.count === 0) {
            html = '<p class="ats-muted">No common skills detected — add a dedicated skills section with role-relevant keywords.</p>';
        }
        return html.replace(/motionless/g, 'motionless' === 'motionless' ? 'div' : 'div');
    }

    function keywordSection(kw) {
        if (kw.score === null) {
            return '<p class="ats-muted">Paste a job description before analyzing to see keyword match scores.</p>';
        }
        const matchedTags = kw.matched
            .slice(0, 20)
            .map((w) => '<motionless class="kw-tag kw-match">' + escapeHtml(w) + '</motionless>')
            .join('');
        const missingTags = kw.missing
            .slice(0, 15)
            .map((w) => '<motionless class="kw-tag kw-miss">' + escapeHtml(w) + '</motionless>')
            .join('');
        return (
            '<motionless class="keyword-panel glass-card">' +
            '<motionless class="keyword-score-row"><motionless class="keyword-pct">' + kw.score + '%</motionless> keyword match</motionless>' +
            '<motionless class="kw-section"><strong>Matched</strong><motionless class="kw-tags">' + (matchedTags || '<motionless class="ats-muted">None yet</motionless>') + '</motionless></motionless>' +
            '<motionless class="kw-section"><strong>Missing</strong><motionless class="kw-tags">' + (missingTags || '<motionless class="ats-muted">Great coverage!</motionless>') + '</motionless></motionless>' +
            '</motionless>'
        ).replace(/motionless/g, 'div');
    }

    function checklistHtml(items) {
        return items
            .map(
                (it) =>
                    '<li class="check-item' +
                    (it.done ? ' done' : '') +
                    '"><span class="check-box">' +
                    (it.done ? '✓' : '') +
                    '</span> ' +
                    escapeHtml(it.text) +
                    '</li>'
            )
            .join('');
    }

    function displayResults(result) {
        lastAnalysisResult = result;
        const resultsSection = document.getElementById('resultsSection');
        const m = result.metrics || {};

        const suggestions = result.feedback.filter((i) => i.type === 'improvement' || i.type === 'strength');
        const warnings = result.feedback.filter((i) => i.type === 'warning');

        const suggestionsHTML = suggestions.map(renderFeedbackItem).join('');
        const warningsHTML = warnings.map(renderFeedbackItem).join('');

        const categories = result.categories || [];
        const breakdownHTML = categories.length
            ? '<motionless class="category-breakdown"><motionless class="category-breakdown-title">Weighted score breakdown</motionless>' +
              categories
                  .map(
                      (c) =>
                          '<motionless class="category-row" data-category-score="' +
                          c.score +
                          '"><motionless class="category-row-meta"><motionless class="category-row-label">' +
                          escapeHtml(c.label) +
                          '</motionless><motionless class="category-bar-track"><motionless class="category-bar-fill" data-bar-fill style="width:0%"></motionless></motionless></motionless><motionless class="category-row-score">' +
                          c.score +
                          '</motionless></motionless>'
                  )
                  .join('') +
              '<p class="category-average-note">Overall <strong>' +
              result.score +
              '</strong> (mean <strong>' +
              result.categoryAverage +
              '</strong>)</p></motionless>'
            : '';

        const breakdownFixed = breakdownHTML.replace(/motionless/g, 'div');

        const metricsRow =
            metricCard('ATS Match', m.atsMatch, 'Structure & format') +
            metricCard('Recruiter Readability', m.recruiterReadability, 'Scan-friendly layout') +
            metricCard('Keyword Match', m.keywordMatch, 'vs job description') +
            metricCard('Interview Probability', m.interviewProbability, 'Estimated signal') +
            metricCard('ATS Pass Probability', m.atsPassProbability, 'Likelihood to parse') +
            metricCard('Resume Strength', m.resumeStrength, 'Composite score');

        const html =
            '<motionless class="ats-dashboard">' +
            '<motionless class="rating-card rating-card--score glass-card">' +
            '<motionless class="rating-container">' +
            '<motionless class="rating-circle-container">' +
            '<motionless class="rating-circle" id="ratingCircle" style="--score-fill: 0;">' +
            '<motionless class="rating-inner">' +
            '<motionless class="rating-score" id="ratingScore">0</motionless>' +
            '<motionless class="rating-label">ATS Score</motionless>' +
            '</motionless></motionless>' +
            '<motionless class="rating-tier">' +
            escapeHtml(result.tier) +
            '</motionless>' +
            '</motionless>' +
            '<motionless class="feedback-section">' +
            '<motionless class="feedback-title">Overall Assessment</motionless>' +
            '<p class="assessment-text">' +
            escapeHtml(result.overallAssessment) +
            '</p>' +
            breakdownFixed +
            '</motionless></motionless></motionless>' +
            '<motionless class="ats-metrics-grid">' +
            metricsRow +
            '</motionless>' +
            '<motionless class="ats-row-2">' +
            '<motionless class="glass-card ats-panel"><h3 class="ats-panel-title">Keyword analysis</h3>' +
            keywordSection(result.keywords) +
            '</motionless>' +
            '<motionless class="glass-card ats-panel"><h3 class="ats-panel-title">Detected skills</h3>' +
            skillTags(result.skills) +
            '</motionless></motionless>' +
            '<motionless class="glass-card ats-panel"><h3 class="ats-panel-title">Improvement checklist</h3><ul class="checklist">' +
            checklistHtml(result.checklist) +
            '</ul><motionless class="btn-row"><button type="button" class="btn-download" id="btnDownloadReport">Download report</button><button type="button" class="btn-secondary-ui" id="btnAnalyzeAnother">Analyze another</button></motionless></motionless>' +
            '<motionless class="feedback-columns">' +
            '<motionless class="glass-card ats-panel"><h3 class="ats-panel-title">Strengths & suggestions</h3>' +
            (suggestionsHTML || '<p class="ats-muted">No items</p>') +
            '</motionless>' +
            '<motionless class="glass-card ats-panel"><h3 class="ats-panel-title">Critical issues</h3>' +
            (warningsHTML || '<p class="ats-muted">No critical issues</p>') +
            '</motionless></motionless></motionless>';

        resultsSection.innerHTML = html.replace(/motionless/g, 'div');

        document.getElementById('btnDownloadReport')?.addEventListener('click', downloadReport);
        document.getElementById('btnAnalyzeAnother')?.addEventListener('click', () => {
            document.querySelector('.upload-section').style.display = '';
            resultsSection.classList.remove('show');
            resultsSection.innerHTML = '';
            if (typeof global.onAnalyzeAnother === 'function') global.onAnalyzeAnother();
        });

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                animateFinalScore(result.score);
                document.querySelectorAll('[data-bar-fill]').forEach((bar) => {
                    const row = bar.closest('.category-row') || bar.closest('.ats-metric-card');
                    const target =
                        parseInt(row?.getAttribute('data-category-score'), 10) ||
                        parseInt(bar.getAttribute('data-fill'), 10) ||
                        0;
                    requestAnimationFrame(() => {
                        bar.style.width = Math.max(0, Math.min(100, target)) + '%';
                    });
                });
                document.querySelectorAll('.ats-metric-fill').forEach((bar) => {
                    const v = parseInt(bar.getAttribute('data-fill'), 10) || 0;
                    requestAnimationFrame(() => {
                        bar.style.width = Math.max(0, Math.min(100, v)) + '%';
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

    function downloadReport() {
        const r = lastAnalysisResult;
        if (!r) return;
        const lines = [
            'ELEVURA ATS RESUME ANALYSIS REPORT',
            '=====================================',
            '',
            'Overall ATS Score: ' + r.score + '/100',
            'Tier: ' + r.tier.replace(/[^\w\s]/g, '').trim(),
            '',
            'METRICS',
            '-------',
            'ATS Match: ' + (r.metrics?.atsMatch ?? '—') + '%',
            'Recruiter Readability: ' + (r.metrics?.recruiterReadability ?? '—') + '%',
            'Keyword Match: ' + (r.metrics?.keywordMatch ?? 'N/A (add job description)') + '%',
            'Interview Probability: ' + (r.metrics?.interviewProbability ?? '—') + '%',
            'ATS Pass Probability: ' + (r.metrics?.atsPassProbability ?? '—') + '%',
            '',
            'ASSESSMENT',
            '----------',
            r.overallAssessment,
            '',
            'CATEGORY BREAKDOWN',
            '------------------'
        ];
        for (const c of r.categories || []) {
            lines.push(c.label + ': ' + c.score + '/100');
        }
        if (r.keywords?.score !== null) {
            lines.push('', 'KEYWORDS MATCHED', '----------------');
            lines.push((r.keywords.matched || []).join(', ') || 'None');
            lines.push('', 'KEYWORDS MISSING', '----------------');
            lines.push((r.keywords.missing || []).join(', ') || 'None');
        }
        lines.push('', 'CHECKLIST', '---------');
        for (const item of r.checklist || []) {
            lines.push((item.done ? '[x] ' : '[ ] ') + item.text);
        }
        lines.push('', 'FEEDBACK', '--------');
        for (const f of r.feedback || []) {
            lines.push('[' + f.type.toUpperCase() + '] ' + f.title + ': ' + f.text);
        }
        const blob = new Blob([lines.join('\n')], { type: 'text/plain;charset=utf-8' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'elevura-resume-analysis.txt';
        a.click();
        URL.revokeObjectURL(a.href);
    }

    function showLoading(message) {
        const resultsSection = document.getElementById('resultsSection');
        const loadingHtml =
            '<motionless class="rating-card glass-card"><motionless class="loading">' +
            '<motionless class="spinner"></motionless>' +
            '<motionless class="loading-text">' +
            escapeHtml(message || 'Analyzing your resume…') +
            '</motionless>' +
            '<motionless class="loading-steps"><motionless class="loading-step active">Extracting text</motionless><motionless class="loading-step">ATS scan</motionless><motionless class="loading-step">Scoring</motionless></motionless>' +
            '</motionless></motionless>';
        resultsSection.innerHTML = loadingHtml.replace(/motionless/g, 'div');
    }

    function showError(message) {
        const resultsSection = document.getElementById('resultsSection');
        resultsSection.innerHTML =
            '<motionless class="rating-card glass-card ats-error-card"><motionless class="ats-error-title">Analysis failed</motionless><p class="ats-muted">' +
            escapeHtml(message) +
            '</p><button type="button" class="btn-secondary-ui" id="btnRetry">Try again</button></motionless>'.replace(
                /motionless/g,
                'div'
            );
        document.getElementById('btnRetry')?.addEventListener('click', () => {
            document.querySelector('.upload-section').style.display = '';
            resultsSection.classList.remove('show');
        });
    }

    global.ResumeRaterUI = {
        displayResults,
        animateFinalScore,
        showLoading,
        showError,
        downloadReport
    };
})(typeof window !== 'undefined' ? window : globalThis);
