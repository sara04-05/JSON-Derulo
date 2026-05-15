/**
 * ElevUra AI CV Writer
 * Multi-step resume form → live preview → PDF download
 */
(function () {
    'use strict';

    /* ====== STATE ====== */
    let currentStep = 0;
    const totalSteps = 5;

    /* ====== DOM REFS ====== */
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);

    /* ====== STEP NAVIGATION ====== */
    function showStep(idx) {
        $$('.step-panel').forEach((p, i) => {
            p.classList.toggle('active', i === idx);
        });
        $$('.step-dot').forEach((d, i) => {
            d.classList.remove('active');
            if (i < idx) d.classList.add('completed');
            else d.classList.remove('completed');
            if (i === idx) d.classList.add('active');
        });
        currentStep = idx;
        if (idx === 4) renderPreview();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function nextStep() {
        if (currentStep < totalSteps - 1) showStep(currentStep + 1);
    }
    function prevStep() {
        if (currentStep > 0) showStep(currentStep - 1);
    }

    /* ====== DYNAMIC ENTRIES ====== */
    let expCount = 0;
    let eduCount = 0;

    function addExperience() {
        expCount++;
        const id = expCount;
        const html = `
        <div class="entry-block" data-exp-id="${id}">
            <button type="button" class="remove-entry" data-remove-exp="${id}" title="Remove">×</button>
            <div class="row">
                <div class="field">
                    <label>Job Title *</label>
                    <input type="text" class="exp-title" placeholder="e.g. Frontend Developer">
                </div>
                <div class="field">
                    <label>Company *</label>
                    <input type="text" class="exp-company" placeholder="e.g. Google">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label>Start Date</label>
                    <input type="text" class="exp-start" placeholder="e.g. Jan 2022">
                </div>
                <div class="field">
                    <label>End Date</label>
                    <input type="text" class="exp-end" placeholder="e.g. Present">
                </div>
            </div>
            <div class="field">
                <label>Description / Key Achievements</label>
                <textarea class="exp-desc" rows="4" placeholder="• Led migration of legacy codebase to React, improving load times by 40%&#10;• Mentored 3 junior developers across 2 agile teams"></textarea>
                <p class="hint">Use bullet points (•) for best results on the final resume.</p>
            </div>
        </div>`;
        $('#experienceList').insertAdjacentHTML('beforeend', html);
    }

    function addEducation() {
        eduCount++;
        const id = eduCount;
        const html = `
        <div class="entry-block" data-edu-id="${id}">
            <button type="button" class="remove-entry" data-remove-edu="${id}" title="Remove">×</button>
            <div class="row">
                <div class="field">
                    <label>Degree / Qualification *</label>
                    <input type="text" class="edu-degree" placeholder="e.g. B.Sc. Computer Science">
                </div>
                <div class="field">
                    <label>Institution *</label>
                    <input type="text" class="edu-school" placeholder="e.g. MIT">
                </div>
            </div>
            <div class="row">
                <div class="field">
                    <label>Start Year</label>
                    <input type="text" class="edu-start" placeholder="e.g. 2018">
                </div>
                <div class="field">
                    <label>End Year</label>
                    <input type="text" class="edu-end" placeholder="e.g. 2022">
                </div>
            </div>
            <div class="field">
                <label>Details (optional)</label>
                <textarea class="edu-desc" rows="2" placeholder="e.g. Dean's List, GPA 3.9/4.0, Thesis on ML"></textarea>
            </div>
        </div>`;
        $('#educationList').insertAdjacentHTML('beforeend', html);
    }

    /* ====== COLLECT FORM DATA ====== */
    function collectData() {
        const data = {
            fullName: $('#fullName').value.trim(),
            jobTitle: $('#jobTitle').value.trim(),
            email: $('#email').value.trim(),
            phone: $('#phone').value.trim(),
            location: $('#location').value.trim(),
            linkedin: $('#linkedin').value.trim(),
            summary: $('#summary').value.trim(),
            skills: $('#skills').value.trim(),
            languages: $('#languages').value.trim(),
            certifications: $('#certifications').value.trim(),
            interests: $('#interests').value.trim(),
            experience: [],
            education: []
        };

        $$('#experienceList .entry-block').forEach(block => {
            data.experience.push({
                title: block.querySelector('.exp-title').value.trim(),
                company: block.querySelector('.exp-company').value.trim(),
                start: block.querySelector('.exp-start').value.trim(),
                end: block.querySelector('.exp-end').value.trim(),
                desc: block.querySelector('.exp-desc').value.trim()
            });
        });

        $$('#educationList .entry-block').forEach(block => {
            data.education.push({
                degree: block.querySelector('.edu-degree').value.trim(),
                school: block.querySelector('.edu-school').value.trim(),
                start: block.querySelector('.edu-start').value.trim(),
                end: block.querySelector('.edu-end').value.trim(),
                desc: block.querySelector('.edu-desc').value.trim()
            });
        });

        return data;
    }

    /* ====== RENDER PREVIEW ====== */
    function renderPreview() {
        const d = collectData();
        const accent = $('#accentColor').value;
        const template = $('#templateSelect').value;
        const page = $('#resumePage');

        page.style.setProperty('--resume-accent', accent);
        page.className = 'resume-page';
        if (template !== 'modern') page.classList.add('template-' + template);

        // Escape HTML
        const esc = (s) => {
            const div = document.createElement('div');
            div.textContent = s;
            return div.innerHTML;
        };

        // Executive template uses a completely different two-column layout
        if (template === 'executive') {
            page.innerHTML = buildExecutiveHTML(d, esc);
            return;
        }

        let html = '';

        // -- Header --
        html += `<div class="resume-header">
            <div class="resume-header-left">
                <h2>${esc(d.fullName || 'Your Name')}</h2>
                ${d.jobTitle ? `<div class="resume-title">${esc(d.jobTitle)}</div>` : ''}
            </div>
            <div class="resume-header-right">`;
        if (d.email) html += `${esc(d.email)}<br>`;
        if (d.phone) html += `${esc(d.phone)}<br>`;
        if (d.location) html += `${esc(d.location)}<br>`;
        if (d.linkedin) html += `<a href="#">${esc(d.linkedin)}</a>`;
        html += `</div></div>`;

        // -- Summary --
        if (d.summary) {
            html += `<div class="resume-section">
                <div class="resume-section-title">Professional Summary</div>
                <div class="resume-summary">${esc(d.summary)}</div>
            </div>`;
        }

        // -- Experience --
        html += buildExperienceHTML(d, esc);

        // -- Education --
        html += buildEducationHTML(d, esc);

        // -- Skills --
        if (d.skills) {
            const tags = d.skills.split(',').map(s => s.trim()).filter(Boolean);
            html += `<div class="resume-section">
                <div class="resume-section-title">Skills</div>
                <div class="resume-skills-list">
                    ${tags.map(t => `<span class="resume-skill-tag">${esc(t)}</span>`).join('')}
                </div>
            </div>`;
        }

        // -- Languages --
        if (d.languages) {
            html += `<div class="resume-section">
                <div class="resume-section-title">Languages</div>
                <div class="resume-lang-line">${esc(d.languages)}</div>
            </div>`;
        }

        // -- Certifications --
        if (d.certifications) {
            const certs = d.certifications.split('\n').map(s => s.trim()).filter(Boolean);
            html += `<div class="resume-section">
                <div class="resume-section-title">Certifications</div>
                ${certs.map(c => `<div class="resume-cert-line">• ${esc(c)}</div>`).join('')}
            </div>`;
        }

        // -- Interests --
        if (d.interests) {
            html += `<div class="resume-section">
                <div class="resume-section-title">Interests</div>
                <div class="resume-lang-line">${esc(d.interests)}</div>
            </div>`;
        }

        page.innerHTML = html;
    }

    /* ====== SHARED SECTION BUILDERS ====== */
    function buildExperienceHTML(d, esc) {
        if (d.experience.length === 0 || !d.experience.some(e => e.title || e.company)) return '';
        let html = `<div class="resume-section">
            <div class="resume-section-title">Work Experience</div>`;
        d.experience.forEach(e => {
            if (!e.title && !e.company) return;
            const dateStr = [e.start, e.end].filter(Boolean).join(' — ');
            html += `<div class="resume-item">
                <div class="resume-item-header">
                    <div class="resume-item-title">${esc(e.title)}</div>
                    ${dateStr ? `<div class="resume-item-date">${esc(dateStr)}</div>` : ''}
                </div>
                ${e.company ? `<div class="resume-item-sub">${esc(e.company)}</div>` : ''}
                ${e.desc ? `<div class="resume-item-desc">${esc(e.desc)}</div>` : ''}
            </div>`;
        });
        html += `</div>`;
        return html;
    }

    function buildEducationHTML(d, esc) {
        if (d.education.length === 0 || !d.education.some(e => e.degree || e.school)) return '';
        let html = `<div class="resume-section">
            <div class="resume-section-title">Education</div>`;
        d.education.forEach(e => {
            if (!e.degree && !e.school) return;
            const dateStr = [e.start, e.end].filter(Boolean).join(' — ');
            html += `<div class="resume-item">
                <div class="resume-item-header">
                    <div class="resume-item-title">${esc(e.degree)}</div>
                    ${dateStr ? `<div class="resume-item-date">${esc(dateStr)}</div>` : ''}
                </div>
                ${e.school ? `<div class="resume-item-sub">${esc(e.school)}</div>` : ''}
                ${e.desc ? `<div class="resume-item-desc">${esc(e.desc)}</div>` : ''}
            </div>`;
        });
        html += `</div>`;
        return html;
    }

    /* ====== EXECUTIVE TEMPLATE (TWO-COLUMN) ====== */
    function buildExecutiveHTML(d, esc) {
        // Left sidebar: name, contact, skills, languages, certs
        let sidebar = `<div class="resume-sidebar">`;
        sidebar += `<h2>${esc(d.fullName || 'Your Name')}</h2>`;
        if (d.jobTitle) sidebar += `<div class="resume-title">${esc(d.jobTitle)}</div>`;
        sidebar += `<div class="sidebar-contact">`;
        if (d.email) sidebar += `${esc(d.email)}<br>`;
        if (d.phone) sidebar += `${esc(d.phone)}<br>`;
        if (d.location) sidebar += `${esc(d.location)}<br>`;
        if (d.linkedin) sidebar += `${esc(d.linkedin)}`;
        sidebar += `</div>`;

        if (d.skills) {
            const tags = d.skills.split(',').map(s => s.trim()).filter(Boolean);
            sidebar += `<div class="sidebar-section-title">Skills</div>
            <div class="sidebar-skills">${tags.map(t => `<span class="sidebar-skill-tag">${esc(t)}</span>`).join('')}</div>`;
        }
        if (d.languages) {
            sidebar += `<div class="sidebar-section-title">Languages</div>
            <div class="sidebar-text">${esc(d.languages)}</div>`;
        }
        if (d.certifications) {
            const certs = d.certifications.split('\n').map(s => s.trim()).filter(Boolean);
            sidebar += `<div class="sidebar-section-title">Certifications</div>
            <div class="sidebar-text">${certs.map(c => `• ${esc(c)}`).join('<br>')}</div>`;
        }
        if (d.interests) {
            sidebar += `<div class="sidebar-section-title">Interests</div>
            <div class="sidebar-text">${esc(d.interests)}</div>`;
        }
        sidebar += `</div>`;

        // Right main: summary, experience, education
        let main = `<div class="resume-main">`;
        if (d.summary) {
            main += `<div class="resume-section">
                <div class="resume-section-title">Professional Summary</div>
                <div class="resume-summary">${esc(d.summary)}</div>
            </div>`;
        }
        main += buildExperienceHTML(d, esc);
        main += buildEducationHTML(d, esc);
        main += `</div>`;

        return sidebar + main;
    }

    /* ====== PDF DOWNLOAD ====== */
    function downloadPDF() {
        const page = $('#resumePage');
        const accent = $('#accentColor').value;
        const name = ($('#fullName').value.trim() || 'Resume');

        // Open a clean print window with just the resume
        const printWin = window.open('', '_blank');
        printWin.document.write(`<!DOCTYPE html>
<html><head>
<title>${name} - Resume</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Georgia&display=swap" rel="stylesheet">
<link rel="stylesheet" href="CVwriter.css">
<style>
    body { margin: 0; padding: 0; background: #fff; }
    .resume-page {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        background: #fff;
        --resume-accent: ${accent};
    }
    @media print {
        body { margin: 0; }
        .resume-page { width: 100%; min-height: auto; padding: 44px 40px; }
        @page { size: A4; margin: 0; }
    }
</style>
</head><body>
<div class="resume-page ${page.className.replace('resume-page', '').trim()}" style="--resume-accent:${accent}">
${page.innerHTML}
</div>
<script>
    window.onload = function() {
        setTimeout(function() { window.print(); }, 400);
    };
</script>
</body></html>`);
        printWin.document.close();
    }

    /* ====== EVENT WIRING ====== */
    function init() {
        // Step navigation buttons
        $('#btnNext0').addEventListener('click', nextStep);
        $('#btnNext1').addEventListener('click', nextStep);
        $('#btnNext2').addEventListener('click', nextStep);
        $('#btnNext3').addEventListener('click', nextStep);
        $('#btnBack1').addEventListener('click', prevStep);
        $('#btnBack2').addEventListener('click', prevStep);
        $('#btnBack3').addEventListener('click', prevStep);
        $('#btnBack4').addEventListener('click', prevStep);

        // Step dot navigation
        $$('.step-dot').forEach(dot => {
            dot.addEventListener('click', () => {
                const idx = parseInt(dot.dataset.step, 10);
                showStep(idx);
            });
        });

        // Add entries
        $('#addExperience').addEventListener('click', addExperience);
        $('#addEducation').addEventListener('click', addEducation);

        // Remove entries (delegated)
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-remove-exp]')) {
                const id = e.target.closest('[data-remove-exp]').dataset.removeExp;
                const block = document.querySelector(`[data-exp-id="${id}"]`);
                if (block) block.remove();
            }
            if (e.target.closest('[data-remove-edu]')) {
                const id = e.target.closest('[data-remove-edu]').dataset.removeEdu;
                const block = document.querySelector(`[data-edu-id="${id}"]`);
                if (block) block.remove();
            }
        });

        // Download buttons
        $('#btnDownload').addEventListener('click', downloadPDF);
        $('#btnDownload2').addEventListener('click', downloadPDF);

        // Template & color changes re-render preview
        $('#templateSelect').addEventListener('change', renderPreview);
        $('#accentColor').addEventListener('change', renderPreview);

        // Seed one blank experience and education entry
        addExperience();
        addEducation();
    }

    /* ====== BOOT ====== */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
