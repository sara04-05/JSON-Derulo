<div class="mi-wrap">
        <header class="header">
            <div class="badge">ElevUra AI</div>
            <h1>Mock <span>Interview</span> Coach</h1>
            <p class="sub">Enter the role you are interviewing for, answer each prompt with your voice, then receive structured feedback on how strong your responses were.</p>
        </header>

        <!-- Setup -->
        <section id="stepSetup" class="card">
            <div class="pill">Step 1 — Role</div>
            <div class="field">
                <label for="jobTitle">Job position you are applying for *</label>
                <input type="text" id="jobTitle" placeholder="e.g. Junior Data Analyst at a healthcare company" autocomplete="off">
                <p class="hint">Questions are tailored using this title. Be specific about seniority and domain when you can.</p>
            </div>
            <div class="row">
                <div class="field">
                    <label for="questionCount">Number of questions</label>
                    <select id="questionCount">
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5" selected>5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                    </select>
                </div>
                <div class="field">
                    <label for="geminiKey">Gemini API key (optional)</label>
                    <input type="password" id="geminiKey" placeholder="Paste key for smarter Q&amp;A" autocomplete="off">
                </div>
            </div>
            <p class="hint">If you add a <a href="https://aistudio.google.com/apikey" target="_blank" rel="noopener">Google AI Studio</a> key, questions and final scoring use Gemini. If the browser blocks the request (CORS) or you leave this blank, the app uses built-in role templates and a local rubric instead.</p>
            <div class="btn-row" style="margin-top: 20px;">
                <button type="button" class="btn btn-primary" id="btnStart">Start mock interview</button>
            </div>
            <p id="setupError" class="hint err hidden" style="margin-top: 14px;"></p>
        </section>

        <!-- Interview -->
        <section id="stepInterview" class="card hidden">
            <div class="pill">Step 2 — Answer with voice</div>
            <div class="progress"><div id="progressBar"></div></div>
            <div class="q-label" id="qIndexLabel"></div>
            <p class="question-text" id="questionText"></p>
            <div class="mic-wrap">
                <button type="button" class="mic" id="btnMic" title="Toggle microphone" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg></button>
                <div class="status" id="listenStatus">Tap the microphone, then speak your answer. Tap again to stop.</div>
            </div>
            <div class="field">
                <label for="transcript">Transcript (edit if the caption is wrong)</label>
                <textarea id="transcript" placeholder="Your spoken answer appears here…"></textarea>
            </div>
            <div class="btn-row">
                <button type="button" class="btn btn-ghost" id="btnSkip">Skip question</button>
                <button type="button" class="btn btn-primary" id="btnSubmitAnswer">Submit answer</button>
            </div>
            <p id="interviewError" class="hint err hidden" style="margin-top: 12px;"></p>
        </section>

        <!-- Results -->
        <section id="stepResults" class="card hidden">
            <div class="pill">Step 3 — Feedback</div>
            <div class="results-head">
                <div>
                    <div class="q-label" style="margin-bottom:4px;">Overall interview score</div>
                    <div class="big-score" id="overallScore">0</div>
                    <div class="tier" id="overallTier"></div>
                </div>
            </div>
            <p class="summary" id="overallSummary"></p>
            <p id="saveStatus" class="hint save-status hidden" role="status"></p>
            <div id="perAnswer"></div>
            <div class="btn-row" style="margin-top: 8px;">
                <button type="button" class="btn btn-primary" id="btnRestart">New interview</button>
            </div>
        </section>

        <p class="footer-note">Voice capture uses your browser’s speech recognition (Chrome or Edge recommended). Use HTTPS or localhost for reliable microphone access.</p>
                    </div>
