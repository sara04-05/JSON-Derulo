<section class="career-path-works">
   <span class="wow-badge">PRACTICE &amp; INSTANT FEEDBACK</span>
    <h3>Study <span> Buddy</span> AI</h3>
    <div class="career-path-grid">
        <div class="career-path-card purple" data-sb-type="quiz" data-protected-tool="career-path">
            <div class="career-path-icon" aria-hidden="true"></div>
            <div class="career-path-card-title">Adaptive Quizzes</div>
            <div class="career-path-card-desc">Dynamically generated questions that adjust difficulty based on your performance and aspiring learning goals.</div>
            <button type="button" class="career-path-btn" data-sb-type="quiz" data-protected-tool="career-path">Execute</button>
        </div>
        <div class="career-path-card pink" data-sb-type="flashcard" data-protected-tool="career-path">
            <div class="career-path-icon" aria-hidden="true"></div>
            <div class="career-path-card-title">Personalized Flashcards</div>
            <div class="career-path-card-desc">AI creates structured interview prep cards based on your role, seniority, and industry focus.</div>
            <button type="button" class="career-path-btn" data-sb-type="flashcard" data-protected-tool="career-path">Execute</button>
        </div>
    </div>

    <!-- Career Prep WORKSPACE (Hidden by default) -->
    <div id="career-path-workspace" class="wow-section hidden" style="margin-top: 40px; width: 100%; max-width: 900px;">
        <div class="wow-layout">
            <div class="wow-content-card">
                <span class="wow-badge" id="sb-workspace-badge">STUDY PREP</span>
                <h3 id="sb-workspace-title">Generate <span>Interview Prep</span></h3>

                <div id="sb-setup-form" class="mi-setup">
                    <input type="hidden" id="sb-type" value="quiz">

                    <div class="field">
                        <label for="sb-jobTitle">Job Title / Position *</label>
                        <input type="text" id="sb-jobTitle" placeholder="e.g. Senior Software Engineer" autocomplete="off">
                    </div>

                    <div class="row">
                        <div class="field">
                            <label for="sb-seniority">Seniority Level</label>
                            <select id="sb-seniority">
                                <option value="Junior">Junior</option>
                                <option value="Mid-Level" selected>Mid-Level</option>
                                <option value="Senior">Senior</option>
                                <option value="Lead/Manager">Lead/Manager</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="sb-industry">Industry / Domain</label>
                            <input type="text" id="sb-industry" placeholder="e.g. Fintech, E-commerce" autocomplete="off">
                        </div>
                    </div>

                    <div class="field">
                        <label for="sb-skills">Key Skills / Keywords</label>
                        <input type="text" id="sb-skills" placeholder="e.g. React, Node.js, System Design" autocomplete="off">
                    </div>

                    <div class="field">
                        <label for="sb-context">Job Description / Context (Optional)</label>
                        <textarea id="sb-context" placeholder="Paste the job description or specific topics you want to study..." rows="3"></textarea>
                    </div>

                    <div class="btn-row mi-btn-row">
                        <button type="button" class="btn btn-primary" id="btnGenerateStudy" data-protected-tool="career-path">Generate Materials</button>
                    </div>
                    <p id="sb-error" class="hint err hidden"></p>
                </div>

                <!-- RESULTS AREA (Hidden by default) -->
                <div id="sb-results" class="hidden" style="margin-top: 30px;">
                    <p id="sb-warning" class="hint err hidden" role="status"></p>
                    <div id="sb-content-container"></div>
                    <div class="btn-row mi-btn-row" style="margin-top: 20px;">
                        <button type="button" class="btn btn-primary" id="btnResetSB">New Generation</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
