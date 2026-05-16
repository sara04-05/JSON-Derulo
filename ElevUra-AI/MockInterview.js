(function () {
    const GEMINI_MODEL = 'gemini-2.0-flash';

    const stepSetup = document.getElementById('stepSetup');
    const stepInterview = document.getElementById('stepInterview');
    const stepResults = document.getElementById('stepResults');
    const jobTitleEl = document.getElementById('jobTitle');
    const questionCountEl = document.getElementById('questionCount');
    const geminiKeyEl = document.getElementById('geminiKey');
    const setupError = document.getElementById('setupError');
    const btnStart = document.getElementById('btnStart');
    const progressBar = document.getElementById('progressBar');
    const qIndexLabel = document.getElementById('qIndexLabel');
    const questionText = document.getElementById('questionText');
    const btnMic = document.getElementById('btnMic');
    const listenStatus = document.getElementById('listenStatus');
    const transcriptEl = document.getElementById('transcript');
    const btnSubmitAnswer = document.getElementById('btnSubmitAnswer');
    const btnSkip = document.getElementById('btnSkip');
    const interviewError = document.getElementById('interviewError');
    const overallScore = document.getElementById('overallScore');
    const overallTier = document.getElementById('overallTier');
    const overallSummary = document.getElementById('overallSummary');
    const perAnswer = document.getElementById('perAnswer');
    const btnRestart = document.getElementById('btnRestart');

    /** @type {string[]} */
    let questions = [];
    /** @type {{ question: string, answer: string }[]} */
    let sessionAnswers = [];
    let jobTitle = '';
    let apiKey = '';
    let qIndex = 0;

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    /** @type {SpeechRecognition | null} */
    let recognition = null;
    let listening = false;

    function show(el, on) {
        el.classList.toggle('hidden', !on);
    }

    function tierFromScore(s) {
        if (s >= 90) return 'Excellent';
        if (s >= 80) return 'Strong';
        if (s >= 70) return 'Solid';
        if (s >= 60) return 'Developing';
        return 'Needs practice';
    }

    const BEHAVIORAL = [
        'Tell me about a time you had a conflict with a teammate or stakeholder. How did you resolve it?',
        'Describe a situation where you missed a deadline or made a mistake. What did you learn?',
        'Give an example of a goal you set for yourself and how you measured progress toward it.',
        'Tell me about a time you had to prioritize competing requests. What framework did you use?',
        'Describe a moment you received tough feedback. How did you respond and what changed afterward?'
    ];

    const BANKS = [
        {
            re: /software|engineer|developer|devops|sre|full[\s-]?stack|backend|frontend|web/i,
            qs: [
                'Walk me through how you would debug a production issue that only affects a small subset of users.',
                'How do you balance shipping quickly with maintaining code quality on {{ROLE}}?',
                'Explain a technical decision you disagreed with. How did you move the discussion forward?',
                'What is your approach to reviewing pull requests and mentoring less experienced engineers?',
                'Describe how you would design observability for a new service your team is launching.'
            ]
        },
        {
            re: /data|analyst|analytics|bi\b|business intelligence|scientist|machine learning|\bml\b|\bai\b/i,
            qs: [
                'How would you explain a complex analytical finding to a non-technical executive for {{ROLE}}?',
                'Tell me about a dataset or metric definition that was ambiguous. How did you clarify it?',
                'Describe an analysis where your initial hypothesis was wrong. What did you do next?',
                'How do you validate that a dashboard or report is trustworthy before it is widely used?',
                'What is your process for translating a business question into an analytical plan?'
            ]
        },
        {
            re: /product|pm\b|project manager|program manager/i,
            qs: [
                'How do you decide what not to build when stakeholders disagree on priorities for {{ROLE}}?',
                'Tell me about a roadmap change forced by market or legal constraints. How did you communicate it?',
                'Describe how you use data and user research together when shaping a feature.',
                'How do you run a discovery phase when requirements are fuzzy?',
                'Give an example of managing risk on a high visibility launch.'
            ]
        },
        {
            re: /sales|account|business development|\bbd\b|revenue/i,
            qs: [
                'How do you prepare for a first meeting with a strategic prospect as {{ROLE}}?',
                'Tell me about losing a deal you expected to win. What did you change afterward?',
                'Describe how you collaborate with solutions or product teams during a complex sales cycle.',
                'How do you qualify opportunities so your pipeline stays realistic?',
                'Share an example of turning a skeptical stakeholder into a champion.'
            ]
        },
        {
            re: /market|growth|seo|content|social|brand|communications/i,
            qs: [
                'Describe a campaign or initiative you owned. How did you measure success for {{ROLE}}?',
                'Tell me about a channel that underperformed. What experiments did you run?',
                'How do you align creative ideas with business goals and budget constraints?',
                'Explain how you would launch a new product line to a cold audience.',
                'Share a time you had to protect brand reputation during a sensitive moment.'
            ]
        },
        {
            re: /design|ux|ui|user experience/i,
            qs: [
                'Walk me through how you would critique your own portfolio piece for {{ROLE}}.',
                'Tell me about a time user research contradicted a stakeholder’s opinion. What happened?',
                'How do you hand off designs to engineering so quality stays high?',
                'Describe how you prioritize accessibility alongside visual polish.',
                'What is your process for validating a new interaction pattern before build?'
            ]
        },
        {
            re: /support|customer success|csm|helpdesk|service/i,
            qs: [
                'Describe how you de-escalate an angry customer while protecting company policy.',
                'Tell me about a recurring customer issue you helped eliminate at the root cause.',
                'How do you decide when an issue should be escalated to engineering or leadership?',
                'What signals do you watch to know a customer account is at risk?',
                'Share an example of turning a support interaction into an expansion opportunity.'
            ]
        },
        {
            re: /hr|people|talent|recruit|human resources/i,
            qs: [
                'How would you explain our employer value proposition to a skeptical candidate for {{ROLE}}?',
                'Tell me about improving a hiring process that was slow or biased.',
                'Describe how you partner with managers on performance concerns.',
                'How do you stay compliant while still giving candidates a human experience?',
                'Share a difficult people situation you navigated confidentially.'
            ]
        },
        {
            re: /finance|accountant|accounting|controller|fp&a|analyst/i,
            qs: [
                'Walk me through how you would tighten month-end close without burning out the team.',
                'Tell me about finding a material error or inconsistency. What was your process?',
                'How do you communicate financial risk to leaders who dislike jargon?',
                'Describe how you would evaluate a new vendor contract for {{ROLE}}.',
                'Share a time you improved forecasting accuracy.'
            ]
        },
        {
            re: /nurse|clinical|healthcare|medical|patient|hospital|pharma/i,
            qs: [
                'Describe how you ensure safety and empathy when workloads spike in {{ROLE}}.',
                'Tell me about coordinating with other disciplines during a complex case.',
                'How do you stay current with evidence or regulations that affect your practice?',
                'Share a time you advocated for a patient or customer outcome.',
                'How do you handle documentation requirements without slowing care?'
            ]
        }
    ];

    function shuffle(arr) {
        const a = arr.slice();
        for (let i = a.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [a[i], a[j]] = [a[j], a[i]];
        }
        return a;
    }

    function localQuestions(role, count) {
        const roleLabel = role.trim() || 'this role';
        const picked = [];
        const lower = role.toLowerCase();

        for (const bank of BANKS) {
            if (bank.re.test(lower)) {
                picked.push(...bank.qs.map((q) => q.replace(/\{\{ROLE\}\}/g, roleLabel)));
            }
        }

        const behavioral = BEHAVIORAL.map((q) => q.replace(/\{\{ROLE\}\}/g, roleLabel));
        const pool = shuffle([...new Set([...picked, ...behavioral])]);
        const out = [];
        let i = 0;
        while (out.length < count && i < pool.length * 2) {
            const q = pool[i % pool.length];
            if (!out.includes(q)) out.push(q);
            i++;
        }
        while (out.length < count) {
            out.push(`Why are you interested in ${roleLabel}, and what impact do you want to make in the first 90 days?`);
        }
        return out.slice(0, count);
    }

    function tokenizeJob(title) {
        return title
            .toLowerCase()
            .replace(/[^a-z0-9\s]/g, ' ')
            .split(/\s+/)
            .filter((w) => w.length > 2 && !['the', 'and', 'for', 'with', 'this', 'that', 'from'].includes(w));
    }

    function localScoreAnswer(question, answer, title) {
        const text = (answer || '').trim();
        const words = text ? text.split(/\s+/) : [];
        const wc = words.length;
        let score = 52;
        const tips = [];

        if (wc < 12) {
            score -= 22;
            tips.push('Aim for at least a few sentences with concrete context so interviewers can picture the situation.');
        } else if (wc < 35) {
            score -= 8;
            tips.push('Add a bit more detail: who was involved, what constraint you faced, and what you did day to day.');
        } else if (wc > 45) {
            score += 6;
        }

        const fillers = (text.match(/\b(um|uh|erm|like|you know)\b/gi) || []).length;
        if (fillers > 0) {
            const pen = Math.min(18, fillers * 3);
            score -= pen;
            tips.push('Reduce filler words by pausing briefly instead—short silences sound more confident than “um.”');
        }

        if (/\d/.test(text)) {
            score += 10;
            tips.push('Nice use of numbers—quantifying impact helps your story stand out.');
        } else {
            tips.push('Where possible, add metrics (percentages, time saved, revenue, volume) even if they are approximate ranges.');
        }

        if (/(result|outcome|impact|because|therefore|so that|lesson|learned|takeaway)/i.test(text)) {
            score += 8;
        } else {
            tips.push('Close the loop: spell out the result or lesson learned so the answer feels complete.');
        }

        if (/(i|we)\s+(built|led|designed|implemented|created|analyzed|coordinated|resolved)/i.test(text)) {
            score += 6;
        } else {
            tips.push('Use clear “I” statements for your actions so your personal contribution is obvious.');
        }

        const keys = tokenizeJob(title);
        let overlap = 0;
        for (const k of keys) {
            if (text.toLowerCase().includes(k)) overlap++;
        }
        if (keys.length) {
            const ratio = overlap / keys.length;
            score += Math.round(ratio * 12);
            if (ratio < 0.2) tips.push('Try weaving in language from the job title or domain so your answer feels targeted to the role.');
        }

        if (/tell me about a time|example|situation|challenge/i.test(question)) {
            if (!/(time|when|project|team|manager|client|stakeholder)/i.test(text)) {
                score -= 5;
                tips.push('Behavioral prompts love a quick scene setter: when, where, who, and what was at stake.');
            }
        }

        score = Math.max(18, Math.min(98, Math.round(score)));
        return { score, feedback: tips.slice(0, 3).join(' ') };
    }

    async function geminiGenerateQuestions(role, count, key) {
        const prompt =
            'You generate interview practice questions. Return ONLY valid JSON with shape {"questions":["..."]}. ' +
            `Generate exactly ${count} distinct questions for someone interviewing for: "${role}". ` +
            'Mix behavioral STAR questions and role-specific situational questions. No numbering prefix in strings.';

        const url = `https://generativelanguage.googleapis.com/v1beta/models/${GEMINI_MODEL}:generateContent?key=${encodeURIComponent(key)}`;
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                contents: [{ role: 'user', parts: [{ text: prompt }] }],
                generationConfig: {
                    temperature: 0.9,
                    responseMimeType: 'application/json'
                }
            })
        });
        if (!res.ok) {
            const errText = await res.text().catch(() => '');
            throw new Error(errText || `Gemini HTTP ${res.status}`);
        }
        const data = await res.json();
        const raw = data?.candidates?.[0]?.content?.parts?.[0]?.text;
        if (!raw) throw new Error('Empty Gemini response');
        const parsed = JSON.parse(raw);
        const qs = parsed.questions;
        if (!Array.isArray(qs) || qs.length < count) throw new Error('Invalid questions JSON');
        return qs.map(String).slice(0, count);
    }

    async function geminiScoreSession(role, qa, key) {
        const prompt =
            'You are a hiring manager. Score each spoken interview answer for clarity, relevance to the question, ' +
            'structure (STAR where appropriate), and impact. Return ONLY JSON: {"overallScore":0-100,"tier":"string","summary":"string","items":[{"question":"","answer":"","score":0-100,"feedback":""}]}.\n' +
            `Role: "${role}".\n` +
            'Q&A JSON:\n' +
            JSON.stringify(qa);

        const url = `https://generativelanguage.googleapis.com/v1beta/models/${GEMINI_MODEL}:generateContent?key=${encodeURIComponent(key)}`;
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                contents: [{ role: 'user', parts: [{ text: prompt }] }],
                generationConfig: {
                    temperature: 0.35,
                    responseMimeType: 'application/json'
                }
            })
        });
        if (!res.ok) {
            const errText = await res.text().catch(() => '');
            throw new Error(errText || `Gemini HTTP ${res.status}`);
        }
        const data = await res.json();
        const raw = data?.candidates?.[0]?.content?.parts?.[0]?.text;
        if (!raw) throw new Error('Empty Gemini response');
        return JSON.parse(raw);
    }

    function initRecognition() {
        if (!SpeechRecognition) return null;
        const rec = new SpeechRecognition();
        rec.lang = (navigator.language || 'en-US');
        rec.continuous = true;
        rec.interimResults = true;
        rec.maxAlternatives = 1;

        rec.onresult = (event) => {
            let finalText = '';
            let interim = '';
            for (let i = event.resultIndex; i < event.results.length; i++) {
                const res = event.results[i];
                const chunk = res[0].transcript;
                if (res.isFinal) finalText += chunk;
                else interim += chunk;
            }
            const base = transcriptEl.dataset.base || '';
            if (finalText) transcriptEl.dataset.base = (base + finalText).trim();
            transcriptEl.value = ((transcriptEl.dataset.base || '') + ' ' + interim).trim();
        };

        rec.onerror = (e) => {
            if (e.error === 'no-speech') return;
            listenStatus.textContent = 'Speech recognition error: ' + e.error + '. You can still type your answer.';
            listenStatus.classList.add('err');
        };

        rec.onend = () => {
            if (listening) {
                try {
                    rec.start();
                } catch (_) {
                    listening = false;
                    btnMic.classList.remove('listening');
                    btnMic.setAttribute('aria-pressed', 'false');
                }
            }
        };

        return rec;
    }

    function setListening(on) {
        listening = on;
        btnMic.classList.toggle('listening', on);
        btnMic.setAttribute('aria-pressed', on ? 'true' : 'false');
        if (!recognition) {
            listenStatus.textContent = 'Speech recognition is not supported in this browser. Please type your answer.';
            listenStatus.classList.add('err');
            return;
        }
        if (on) {
            listenStatus.classList.remove('err');
            listenStatus.textContent = 'Listening… speak naturally, then tap the mic again to stop.';
            transcriptEl.dataset.base = (transcriptEl.value || '').trim();
            try {
                recognition.start();
            } catch (_) {
                try {
                    recognition.stop();
                    recognition.start();
                } catch (e2) {
                    listenStatus.textContent = 'Unable to start listening. Check microphone permissions.';
                    listenStatus.classList.add('err');
                    listening = false;
                    btnMic.classList.remove('listening');
                    btnMic.setAttribute('aria-pressed', 'false');
                }
            }
        } else {
            listenStatus.textContent = 'Tap the microphone when you are ready to answer.';
            try {
                recognition.stop();
            } catch (_) {}
        }
    }

    btnMic.addEventListener('click', () => {
        interviewError.classList.add('hidden');
        if (!recognition) {
            recognition = initRecognition();
            if (!recognition) return;
        }
        setListening(!listening);
    });

    btnStart.addEventListener('click', async () => {
        setupError.classList.add('hidden');
        jobTitle = jobTitleEl.value.trim();
        if (!jobTitle) {
            setupError.textContent = 'Please enter the job position you are applying for before starting.';
            setupError.classList.remove('hidden');
            jobTitleEl.focus();
            return;
        }

        const count = Math.min(8, Math.max(3, parseInt(questionCountEl.value, 10) || 5));
        apiKey = geminiKeyEl.value.trim();
        btnStart.disabled = true;
        btnStart.textContent = 'Preparing questions…';

        try {
            if (apiKey) {
                try {
                    questions = await geminiGenerateQuestions(jobTitle, count, apiKey);
                } catch (e) {
                    console.warn('Gemini question gen failed, using local bank:', e);
                    questions = localQuestions(jobTitle, count);
                }
            } else {
                questions = localQuestions(jobTitle, count);
            }
        } finally {
            btnStart.disabled = false;
            btnStart.textContent = 'Start mock interview';
        }

        sessionAnswers = [];
        qIndex = 0;
        recognition = initRecognition();

        show(stepSetup, false);
        show(stepInterview, true);
        show(stepResults, false);
        renderQuestion();
    });

    function renderQuestion() {
        const total = questions.length;
        const pct = total ? ((qIndex) / total) * 100 : 0;
        progressBar.style.width = pct + '%';
        qIndexLabel.textContent = `Question ${qIndex + 1} of ${total}`;
        questionText.textContent = questions[qIndex];
        transcriptEl.value = '';
        delete transcriptEl.dataset.base;
        interviewError.classList.add('hidden');
        listenStatus.classList.remove('err');
        listenStatus.textContent = 'Tap the microphone, then speak your answer. Tap again to stop.';
        if (listening) setListening(false);
    }

    function advanceOrFinish() {
        if (qIndex >= questions.length - 1) {
            void finishSession();
            return;
        }
        qIndex++;
        renderQuestion();
    }

    btnSubmitAnswer.addEventListener('click', () => {
        if (listening) setListening(false);
        interviewError.classList.add('hidden');
        const ans = transcriptEl.value.trim();
        if (!ans) {
            interviewError.textContent = 'Please record or type an answer before submitting.';
            interviewError.classList.remove('hidden');
            return;
        }
        sessionAnswers.push({ question: questions[qIndex], answer: ans });
        advanceOrFinish();
    });

    btnSkip.addEventListener('click', () => {
        sessionAnswers.push({ question: questions[qIndex], answer: '[Skipped]' });
        advanceOrFinish();
    });

    async function finishSession() {
        show(stepInterview, false);
        show(stepResults, true);
        progressBar.style.width = '100%';

        overallScore.textContent = '…';
        overallTier.textContent = '';
        overallSummary.textContent = 'Scoring your answers…';
        perAnswer.innerHTML = '';

        const qaPayload = sessionAnswers.map((x) => ({ question: x.question, answer: x.answer }));

        if (apiKey) {
            try {
                const g = await geminiScoreSession(jobTitle, qaPayload, apiKey);
                const os = Math.max(0, Math.min(100, Math.round(Number(g.overallScore) || 0)));
                overallScore.textContent = String(os);
                overallTier.textContent = g.tier || tierFromScore(os);
                overallSummary.textContent = g.summary || 'Here is how you did across the mock interview.';

                const items = Array.isArray(g.items) ? g.items : [];
                perAnswer.innerHTML = sessionAnswers
                    .map((row, idx) => {
                        const it = items[idx] || {};
                        const fallback = localScoreAnswer(row.question, row.answer, jobTitle);
                        const sc = Math.max(0, Math.min(100, Math.round(Number(it.score) || fallback.score)));
                        const fb = it.feedback || fallback.feedback;
                        return (
                            '<div class="answer-block">' +
                            '<span class="score-tag">' + sc + '/100</span>' +
                            '<h3>Question ' + (idx + 1) + '</h3>' +
                            '<div class="answer-meta">' + escapeHtml(row.question) + '</div>' +
                            '<div class="answer-body"><strong>Your answer</strong><br>' + escapeHtml(row.answer) + '</div>' +
                            '<div class="answer-body" style="margin-top:10px;"><strong>Feedback</strong><br>' + escapeHtml(fb) + '</div>' +
                            '</div>'
                        );
                    })
                    .join('');
                return;
            } catch (e) {
                console.warn('Gemini scoring failed, using local rubric:', e);
            }
        }

        const localItems = sessionAnswers.map((row) => {
            const r = localScoreAnswer(row.question, row.answer, jobTitle);
            return { ...row, score: r.score, feedback: r.feedback };
        });
        const avg =
            localItems.length === 0
                ? 0
                : Math.round(localItems.reduce((s, x) => s + x.score, 0) / localItems.length);

        overallScore.textContent = String(avg);
        overallTier.textContent = tierFromScore(avg);
        overallSummary.textContent =
            avg >= 80
                ? 'Strong rehearsal: your answers were detailed and mostly structured. Tighten stories further with crisper outcomes.'
                : avg >= 65
                    ? 'Good foundation. Push each answer with clearer stakes, your specific actions, and measurable results.'
                    : 'Keep practicing out loud. Focus on one clear example per question with beginning, middle, and measurable end.';

        perAnswer.innerHTML = localItems
            .map(
                (row, idx) =>
                    '<div class="answer-block">' +
                    '<span class="score-tag">' + row.score + '/100</span>' +
                    '<h3>Question ' + (idx + 1) + '</h3>' +
                    '<div class="answer-meta">' + escapeHtml(row.question) + '</div>' +
                    '<div class="answer-body"><strong>Your answer</strong><br>' + escapeHtml(row.answer) + '</div>' +
                    '<div class="answer-body" style="margin-top:10px;"><strong>Feedback</strong><br>' + escapeHtml(row.feedback) + '</div>' +
                    '</div>'
            )
            .join('');
    }

    function escapeHtml(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    btnRestart.addEventListener('click', () => {
        show(stepResults, false);
        show(stepSetup, true);
        jobTitleEl.focus();
    });
})();
