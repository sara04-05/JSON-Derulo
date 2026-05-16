<div class="cv-wrap">
                    <header class="header">
                        <div class="badge">ElevUra AI</div>
                        <h1>AI <span>CV Writer</span></h1>
                        <p class="sub">Fill in your details below, preview your professional resume in real-time, then download it as a polished PDF — ready to impress.</p>
                    </header>

                    <!-- Progress Steps -->
                    <div class="steps-nav" id="stepsNav">
                        <button class="step-dot active" data-step="0"><span>1</span> Personal</button>
                        <div class="step-line"></div>
                        <button class="step-dot" data-step="1"><span>2</span> Experience</button>
                        <div class="step-line"></div>
                        <button class="step-dot" data-step="2"><span>3</span> Education</button>
                        <div class="step-line"></div>
                        <button class="step-dot" data-step="3"><span>4</span> Skills</button>
                        <div class="step-line"></div>
                        <button class="step-dot" data-step="4"><span>5</span> Preview</button>
                    </div>

                    <!-- Step 0: Personal Info -->
                    <section id="step0" class="card step-panel active">
                        <div class="pill">Step 1 — Personal Information</div>
                        <div class="row">
                            <div class="field">
                                <label for="fullName">Full Name *</label>
                                <input type="text" id="fullName" placeholder="e.g. Sarah Johnson" autocomplete="name">
                            </div>
                            <div class="field">
                                <label for="jobTitle">Job Title / Target Role *</label>
                                <input type="text" id="jobTitle" placeholder="e.g. Senior Software Engineer" autocomplete="organization-title">
                            </div>
                        </div>
                        <div class="row">
                            <div class="field">
                                <label for="email">Email *</label>
                                <input type="text" id="email" placeholder="sarah@example.com" autocomplete="email">
                            </div>
                            <div class="field">
                                <label for="phone">Phone</label>
                                <input type="text" id="phone" placeholder="+1 (555) 123-4567" autocomplete="tel">
                            </div>
                        </div>
                        <div class="row">
                            <div class="field">
                                <label for="location">Location</label>
                                <input type="text" id="location" placeholder="New York, NY" autocomplete="address-level2">
                            </div>
                            <div class="field">
                                <label for="linkedin">LinkedIn / Portfolio URL</label>
                                <input type="text" id="linkedin" placeholder="linkedin.com/in/sarahjohnson">
                            </div>
                        </div>
                        <div class="field">
                            <label for="summary">Professional Summary *</label>
                            <textarea id="summary" placeholder="Write 2-3 sentences highlighting your expertise, years of experience, and key achievements..."></textarea>
                            <p class="hint">A strong summary hooks the recruiter. Focus on impact and specialisation.</p>
                        </div>
                        <div class="btn-row">
                            <button type="button" class="btn btn-primary" id="btnNext0">Continue to Experience →</button>
                        </div>
                    </section>

                    <!-- Step 1: Experience -->
                    <section id="step1" class="card step-panel">
                        <div class="pill">Step 2 — Work Experience</div>
                        <p class="hint" style="margin-bottom:18px;">Add your work history, most recent first. You can add multiple positions.</p>
                        <div id="experienceList"></div>
                        <button type="button" class="btn btn-ghost add-btn" id="addExperience">+ Add Position</button>
                        <div class="btn-row" style="margin-top:18px;">
                            <button type="button" class="btn btn-ghost" id="btnBack1">← Back</button>
                            <button type="button" class="btn btn-primary" id="btnNext1">Continue to Education →</button>
                        </div>
                    </section>

                    <!-- Step 2: Education -->
                    <section id="step2" class="card step-panel">
                        <div class="pill">Step 3 — Education</div>
                        <p class="hint" style="margin-bottom:18px;">Add your educational background.</p>
                        <div id="educationList"></div>
                        <button type="button" class="btn btn-ghost add-btn" id="addEducation">+ Add Education</button>
                        <div class="btn-row" style="margin-top:18px;">
                            <button type="button" class="btn btn-ghost" id="btnBack2">← Back</button>
                            <button type="button" class="btn btn-primary" id="btnNext2">Continue to Skills →</button>
                        </div>
                    </section>

                    <!-- Step 3: Skills -->
                    <section id="step3" class="card step-panel">
                        <div class="pill">Step 4 — Skills & Extras</div>
                        <div class="field">
                            <label for="skills">Technical Skills *</label>
                            <textarea id="skills" placeholder="e.g. JavaScript, React, Node.js, Python, SQL, AWS, Docker..."></textarea>
                            <p class="hint">Separate skills with commas. These will render as tags on your resume.</p>
                        </div>
                        <div class="field">
                            <label for="languages">Languages</label>
                            <input type="text" id="languages" placeholder="e.g. English (Native), Spanish (Fluent), French (Basic)">
                        </div>
                        <div class="field">
                            <label for="certifications">Certifications</label>
                            <textarea id="certifications" rows="3" placeholder="e.g. AWS Solutions Architect, Google Cloud Professional, PMP..."></textarea>
                        </div>
                        <div class="field">
                            <label for="interests">Interests (optional)</label>
                            <input type="text" id="interests" placeholder="e.g. Open source, marathon running, photography">
                        </div>
                        <div class="btn-row" style="margin-top:18px;">
                            <button type="button" class="btn btn-ghost" id="btnBack3">← Back</button>
                            <button type="button" class="btn btn-primary" id="btnNext3">Preview Resume →</button>
                        </div>
                    </section>

                    <!-- Step 4: Preview & Download -->
                    <section id="step4" class="card step-panel preview-step">
                        <div class="pill">Step 5 — Preview & Download</div>
                        <div class="preview-toolbar">
                            <div class="template-selector">
                                <label for="templateSelect">Template</label>
                                <select id="templateSelect">
                                    <option value="modern" selected>Modern</option>
                                    <option value="classic">Classic</option>
                                    <option value="minimal">Minimal</option>
                                    <option value="executive">Executive</option>
                                    <option value="creative">Creative</option>
                                    <option value="elegant">Elegant</option>
                                </select>
                            </div>
                            <div class="template-selector">
                                <label for="accentColor">Accent</label>
                                <select id="accentColor">
                                    <option value="#0ea5e9" selected>Ocean Blue</option>
                                    <option value="#8b5cf6">Violet</option>
                                    <option value="#10b981">Emerald</option>
                                    <option value="#f59e0b">Amber</option>
                                    <option value="#ef4444">Ruby</option>
                                    <option value="#1a1a2e">Midnight</option>
                                </select>
                            </div>
                            <div class="download-actions">
                                <button type="button" class="btn btn-primary download-btn" id="btnDownloadATS" title="Selectable text PDF for ATS systems">
                                    Download ATS PDF
                                </button>
                                <button type="button" class="btn btn-ghost download-btn btn-export-print" id="btnDownloadPrint" title="Opens print dialog — Save as PDF with full styling">
                                    Save as PDF (Print)
                                </button>
                            </div>
                        </div>
                        <p class="export-hint">
                            <strong>ATS PDF:</strong> For resume scoring and ATS optimization<br>
                            <strong>Print PDF:</strong> For job applications with clean formatting
                        </p>
                        <div class="resume-preview-container" id="resumePreviewContainer">
                            <div class="resume-page" id="resumePage">
                                <!-- Filled by JS -->
                            </div>
                        </div>
                        <div class="btn-row btn-row-downloads" style="margin-top:18px;">
                            <button type="button" class="btn btn-ghost" id="btnBack4">← Back to Edit</button>
                            <button type="button" class="btn btn-primary" id="btnDownloadATS2">Download ATS PDF</button>
                            <button type="button" class="btn btn-ghost btn-export-print" id="btnDownloadPrint2">Save as PDF (Print)</button>
                        </div>
                    </section>

                    <p class="footer-note">Your data stays in your browser — nothing is sent to any server.</p>
                </div>
