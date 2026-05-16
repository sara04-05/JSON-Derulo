<div class="content">
            <!-- HEADER -->
            <div class="header">
                <div class="header-badge">ATS POWERED ANALYSIS</div>
                <h1 class="header-title">
                    Resume
                    <span class="header-title-gradient">ATS Analyzer</span>
                </h1>
                <p class="header-subtitle">
                    Professional ATS scoring, keyword matching, skills detection, and recruiter-ready feedback
                </p>
            </div>

            <!-- UPLOAD SECTION -->
            <div class="upload-section">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    </div>
                    <div class="upload-text">Upload Your Resume</div>
                    <div class="upload-subtext">Drag and drop PDF, DOCX, or TXT &mdash; or click to browse</div>
                    <button class="upload-button" onclick="document.getElementById('fileInput').click()">
                        Choose File
                    </button>
                    <input type="file" id="fileInput" accept=".pdf,.docx,.txt,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain" />
                </div>

                <div class="job-description-block">
                    <label for="jobDescription">
                        Target job description
                        <span class="label-hint">(optional &mdash; improves keyword match &amp; ATS alignment)</span>
                    </label>
                    <textarea id="jobDescription" placeholder="Paste the job posting here to compare your resume keywords against role requirements&hellip;" rows="5"></textarea>
                </div>

                <div class="file-info" id="fileInfo">
                    <div>
                        <div class="file-name" id="fileName"></div>
                        <div class="file-size" id="fileSize"></div>
                    </div>
                    <button class="clear-file" onclick="clearFile()">Clear</button>
                </div>

                <button class="analyze-button" id="analyzeButton" onclick="analyzeResume()" disabled>
                    Run ATS Analysis
                </button>
            </div>

            <!-- RESULTS SECTION -->
            <div class="results-section" id="resultsSection">
                <!-- RATING CARD -->
                <div class="rating-card">
                    <div class="rating-container">
                        <div class="rating-circle-container">
                            <div class="rating-circle" id="ratingCircle" style="--score-fill: 0;">
                                <div class="rating-inner">
                                    <div class="rating-score" id="ratingScore">0</div>
                                    <div class="rating-label">Score</div>
                                </div>
                            </div>
                            <div class="rating-tier" id="ratingTier">Analyzing...</div>
                        </div>

                        <div class="feedback-section">
                            <div>
                                <div class="feedback-title">Overall Assessment</div>
                                <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.8;" id="overallAssessment">
                                    Analyzing your resume...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUGGESTIONS SECTION -->
                <div class="rating-card">
                    <div class="feedback-title feedback-title--icon" style="margin-bottom: 24px;">
                        <span class="feedback-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/></svg></span>
                        Suggestions &amp; Improvements
                    </div>
                    <div id="suggestionsContainer"></div>
                </div>

                <!-- WARNINGS SECTION -->
                <div class="rating-card">
                    <div class="feedback-title feedback-title--icon" style="margin-bottom: 24px;">
                        <span class="feedback-title-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span>
                        Areas to Address
                    </div>
                    <div id="warningsContainer"></div>
                </div>
            </div>
        </div>
