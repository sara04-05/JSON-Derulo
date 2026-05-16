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
                    <div class="upload-icon">ðŸ“„</div>
                    <div class="upload-text">Upload Your Resume</div>
                    <div class="upload-subtext">Drag and drop PDF, DOCX, or TXT â€” or click to browse</div>
                    <button class="upload-button" onclick="document.getElementById('fileInput').click()">
                        Choose File
                    </button>
                    <input type="file" id="fileInput" accept=".pdf,.docx,.txt,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain" />
                </div>

                <div class="job-description-block">
                    <label for="jobDescription">
                        Target job description
                        <span class="label-hint">(optional â€” improves keyword match &amp; ATS alignment)</span>
                    </label>
                    <textarea id="jobDescription" placeholder="Paste the job posting here to compare your resume keywords against role requirementsâ€¦" rows="5"></textarea>
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
                    <div class="feedback-title" style="margin-bottom: 24px;">ðŸ’¡ Suggestions & Improvements</div>
                    <div id="suggestionsContainer"></div>
                </div>

                <!-- WARNINGS SECTION -->
                <div class="rating-card">
                    <div class="feedback-title" style="margin-bottom: 24px;">âš ï¸ Areas to Address</div>
                    <div id="warningsContainer"></div>
                </div>
            </div>
        </div>
