<?php
$toolCtaAuthAttr = empty($loggedIn) ? ' data-auth-open="login"' : '';
?>
            <section class="content-area" id="view-command-center">
                <!-- HERO PANEL -->
                <div class="hero-panel">
                    <div class="hero-left">
                        <div class="hero-badge">&gt; SYSTEM INITIALIZED</div>
                        <h1 class="hero-title">
                            Initialize Your <span class="hero-title-gradient">Future Trajectory</span>
                        </h1>
                        <p class="hero-subtitle">
                            Unlock your potential with AI-powered career guidance, CV optimization, and personalized learning paths designed to accelerate your trajectory in an evolving market.
                        </p>
                        <div class="command-input-wrapper">
                            <span class="command-prompt">~ $</span>
                            <input
                                type="text"
                                class="command-input"
                                placeholder="Prompt the command center... (e.g. 'Analyze my CV against...')"
                                autocomplete="off"
                            />
                            <button type="button" class="execute-button">Execute <span class="execute-key" aria-hidden="true">Enter</span></button>
                        </div>
                    </div>

                <div class="hero-right">
                    <div class="hero-wireframe">
                        <img src="images/image.png" alt="3D data network visualization" loading="lazy" decoding="async">
                    </div>
                </div>
                </div>

                <!-- MODULE GRID -->
                <div class="module-grid">
                    <!-- AI Career Coach -->
                    <div class="module-card" id="module-career-coach">
                        <div class="module-header">
                            <div class="module-icon module-icon-accent-purple" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                        </div>
                        <h3 class="module-title">AI Career Coach</h3>
                        <p class="module-description">
                            Career path suggestions, interview prep, skills gap analysis, and salary predictions tailored to your profile.
                        </p>
                    </div>

                    <!-- CV Optimizer -->
                    <div class="module-card" id="module-cv-optimizer">
                        <div class="module-header">
                            <div class="module-icon module-icon-accent-cyan" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"></svg>
                            </div>
                        </div>
                        <h3 class="module-title">CV Optimizer</h3>
                        <p class="module-description">
                            ATS score analysis, grammar fixes, keyword enhancement, and recruiter simulation for maximum impact.
                        </p>
                    </div>

                    <!-- Career Prep (dedicated page) -->
                    <div class="module-card" id="module-study-buddy" data-protected-tool="study-buddy">
                        <div class="module-header">
                            <div class="module-icon module-icon-accent-cyan" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="9" cy="12" r="5"/><circle cx="15" cy="12" r="5"/></svg>
                            </div>
                        </div>
                        <h3 class="module-title">Career Prep</h3>
                        <p class="module-description">
                            Adaptive quizzes, flashcards, and interview prep tailored to your target role and industry.
                        </p>
                    </div>

                    <!-- AI CV Writer -->
                    <div class="module-card" id="module-ai-cv-writer">
                        <div class="module-header">
                            <div class="module-icon module-icon-accent-purple" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </div>
                        </div>
                        <h3 class="module-title">AI CV Writer</h3>
                        <p class="module-description">
                            Writes  CVs, rewrites bullet points, strengthens summaries, and aligns content to your target role.
                        </p>
                    </div>
                </div>

       

        <!-- MOCKINTERVIEW SECTION --> 

               <section class="wow-section" data-protected-block="cv-scoring" id="cv-scoring-section">

    <div class="wow-layout">

        <!-- LEFT TEXT CARD -->
        <div class="wow-content-card">

            <span class="wow-badge">INSTANT FEEDBACK</span>

            <h3>
                Live AI CV <span>Scoring</span>
            </h3>

            <p>
                Upload your CV and watch as our AI instantly analyzes and scores it across multiple dimensions.
            </p>

            <p>
                Get real-time feedback, recruiter simulations, ATS scoring, and improvement suggestions instantly.
            </p>

            <p>
                Understand exactly how employers view your CV using advanced AI heatmap analysis and optimization systems.
            </p>

        </div>

        <!-- RIGHT SCORE CARD -->
        <div class="cv-demo-card">

            <div class="rating-wrapper">
                <div class="rating-circle ring-complete" style="--score-fill: 85;">
                    <div class="rating-inner">
                        <div class="rating-score">85</div>
                        <div class="rating-label">Score</div>
                    </div>
                </div>
            </div>

            <div class="score-details">

                <div class="detail-item">
                    <h4>Readability</h4>
                    <div class="detail-value">94%</div>
                </div>

                <div class="detail-item">
                    <h4>Keywords</h4>
                    <div class="detail-value">88%</div>
                </div>

                <div class="detail-item">
                    <h4>Professionalism</h4>
                    <div class="detail-value">91%</div>
                </div>

                <div class="detail-item">
                    <h4>Overall Quality</h4>
                    <div class="detail-value">87%</div>
                </div>

            </div>

        </div>

    </div>
<p class="protected-trigger" style="
    font-size: 20px;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: white;
    text-align: center;
    grid-column: 1 / -1;
    padding-top: 40px;
    padding-bottom: 0;
    margin-bottom: 0;
">
    To use our model <a href="cv-optimizer.php" class="protected-trigger-link"<?= $toolCtaAuthAttr ?>>press here</a>.
</p>
               </section>

        <!-- AI CV WRITER SECTION -->
        <section class="wow-section" id="cv-writer-section">

    <div class="wow-layout">

        <div class="wow-content-card">

            <span class="wow-badge">BUILD &amp; EXPORT</span>

            <h3>
                AI <span>CV Writer</span>
            </h3>

            <p>
                Build a professional resume step by step with guided personal info, experience, education, and skills sections.
            </p>

            <p>
                AI strengthens your summary, rewrites bullet points for impact, and aligns every line to your target role.
            </p>

            <p>
                Preview templates in real time, then download ATS-friendly or styled PDFs ready to send to employers.
            </p>

        </div>

        <div class="cv-demo-card">

            <div class="cv-writer-steps">
                <span class="cv-writer-step is-active">Personal</span>
                <span class="cv-writer-step">Experience</span>
                <span class="cv-writer-step">Education</span>
                <span class="cv-writer-step">Skills</span>
                <span class="cv-writer-step">Preview</span>
            </div>

            <div class="cv-writer-preview">

                <div class="cv-writer-preview-header">
                    <div class="cv-writer-preview-name">Sarah Johnson</div>
                    <div class="cv-writer-preview-role">Senior Software Engineer</div>
                </div>

                <p class="cv-writer-preview-summary">
                    Results-driven engineer with 6+ years building scalable web products. Led cross-functional teams to deliver features used by 500K+ users.
                </p>

                <div class="cv-writer-bullet">Increased application performance by 40% through React optimization and API caching.</div>
                <div class="cv-writer-bullet">Shipped payment integration reducing checkout drop-off by 18% across mobile and web.</div>
                <div class="cv-writer-bullet">Mentored 4 junior developers and established code review standards for the frontend guild.</div>

                <div class="cv-writer-tags">
                    <span class="cv-writer-tag">React</span>
                    <span class="cv-writer-tag">TypeScript</span>
                    <span class="cv-writer-tag">Node.js</span>
                    <span class="cv-writer-tag">AWS</span>
                </div>

            </div>

            <ul class="cv-writer-features" aria-label="CV Writer capabilities">
                <li class="cv-writer-feature">
                    <span class="cv-writer-feature__label">Templates</span>
                    <span class="cv-writer-feature__value">6</span>
                </li>
                <li class="cv-writer-feature">
                    <span class="cv-writer-feature__label">Export</span>
                    <span class="cv-writer-feature__value">PDF</span>
                </li>
                <li class="cv-writer-feature">
                    <span class="cv-writer-feature__label">AI Rewrite</span>
                    <span class="cv-writer-feature__value">On</span>
                </li>
                <li class="cv-writer-feature">
                    <span class="cv-writer-feature__label">ATS Ready</span>
                    <span class="cv-writer-feature__value">Yes</span>
                </li>
            </ul>

        </div>

    </div>

<p class="protected-trigger" style="
    font-size: 20px;
    line-height: 1.05;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: white;
    text-align: center;
    grid-column: 1 / -1;
    padding-top: 40px;
    padding-bottom: 0;
    margin-bottom: 0;
">
    To use our model <a href="CVwriter.php" class="protected-trigger-link"<?= $toolCtaAuthAttr ?>>press here</a>.
</p>

        </section>

        <!-- CAREER PREP SECTION -->
        <section class="study-buddy-works" id="study-buddy-works">
           <span class="wow-badge">PRACTICE &amp; INSTANT FEEDBACK</span>
            <h3>Career <span>Prep</span></h3>
            <div class="study-buddy-grid">
                <div class="study-buddy-card" data-protected-tool="study-buddy">
                    <div class="study-buddy-icon" aria-hidden="true"></div>
                    <div class="study-buddy-card-title">AI-Powered Explanations</div>
                    <div class="study-buddy-card-desc">Enter any topic and get instant, personalized explanations tailored to your learning level with real-world examples.</div>
                    <button type="button" class="study-buddy-btn" data-protected-tool="study-buddy">Execute</button>
                </div>
                <div class="study-buddy-card purple" data-protected-tool="study-buddy">
                    <div class="study-buddy-icon" aria-hidden="true"></div>
                    <div class="study-buddy-card-title">Adaptive Quizzes</div>
                    <div class="study-buddy-card-desc">Dynamically generated questions that adjust difficulty based on your performance and aspiring learning goals.</div>
                    <button type="button" class="study-buddy-btn" data-protected-tool="study-buddy">Execute</button>
                </div>
                <div class="study-buddy-card pink" data-protected-tool="study-buddy">
                    <div class="study-buddy-icon" aria-hidden="true"></div>
                    <div class="study-buddy-card-title">Personalized Study Plans</div>
                    <div class="study-buddy-card-desc">AI creates structured 4-week roadmaps aligned with your goals, intensity, and schedule for optimal learning.</div>
                    <button type="button" class="study-buddy-btn" data-protected-tool="study-buddy">Execute</button>
                </div>
            </div>
            <p class="protected-trigger wow-section-cta">
                To use our model <a href="study-buddy.php" class="protected-trigger-link"<?= $toolCtaAuthAttr ?>>press here</a>.
            </p>
        </section>

        <section class="wow-section" id="mock-interview" data-protected-block="mock-interview">
            <div class="wow-layout mi-layout">
                <div class="wow-content-card mi-intro-card">
                    <span class="wow-badge">PRACTICE &amp; FEEDBACK</span>
                    <h3>Mock <span>Interview</span> Coach</h3>
                    <div class="mi-intro-copy">
                        <p>Practice role-specific questions with voice capture, then get structured scores across communication, confidence, and professionalism.</p>
                        <p>Our AI tailors prompts to your target job title and seniority, then breaks down each answer with actionable feedback you can apply before the real interview.</p>
                        <p>Results sync to Mission Control so you can track improvement across every mock session.</p>
                    </div>
                    <div class="mi-setup">
                        <div class="pill">Step 1 — Role</div>
                        <div class="field">
                            <label for="miJobTitle">Job position you are applying for *</label>
                            <input type="text" id="miJobTitle" placeholder="e.g. Junior Data Analyst at a healthcare company" autocomplete="off">
                            <p class="hint">Questions are tailored using this title. Be specific about seniority and domain when you can.</p>
                        </div>
                        <div class="row">
                            <div class="field">
                                <label for="miQuestionCount">Number of questions</label>
                                <select id="miQuestionCount">
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5" selected>5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                </select>
                            </div>
                        </div>
                        <div class="btn-row mi-btn-row">
                            <a href="MockInterview.php" class="btn btn-primary" id="btnStart" data-protected-tool="mock-interview">Start mock interview</a>
                        </div>
                    </div>
                </div>

                <div class="cv-demo-card mi-demo-rail">
                    <div class="mi-preview-panel mi-preview-panel--cyan">
                        <div class="pill">Your results preview</div>
                        <div class="mi-preview-score">
                            <div class="q-label">Overall interview score</div>
                            <div class="big-score">87</div>
                            <div class="tier">Strong performance</div>
                        </div>
                    </div>

                    <div class="mi-preview-panel mi-preview-panel--purple">
                        <div class="pill pill--purple">Performance metrics</div>
                        <div class="mi-metric-grid">
                            <div>
                                <div class="mi-metric-label">Confidence</div>
                                <div class="mi-metric-value mi-metric-value--green">92%</div>
                            </div>
                            <div>
                                <div class="mi-metric-label">Communication</div>
                                <div class="mi-metric-value mi-metric-value--cyan">88%</div>
                            </div>
                            <div>
                                <div class="mi-metric-label">Knowledge</div>
                                <div class="mi-metric-value mi-metric-value--purple">91%</div>
                            </div>
                            <div>
                                <div class="mi-metric-label">Professionalism</div>
                                <div class="mi-metric-value mi-metric-value--sky">89%</div>
                            </div>
                        </div>
                    </div>

                    <div class="mi-preview-panel mi-preview-panel--blue">
                        <div class="pill pill--blue">Sample answer feedback</div>
                        <div class="mi-feedback-stack">
                            <div class="mi-feedback-item mi-feedback-item--good">
                                <strong>Question 1: &ldquo;Tell me about yourself&rdquo;</strong>
                                Excellent response with clear structure and relevant details. You highlighted key achievements effectively.
                            </div>
                            <div class="mi-feedback-item mi-feedback-item--warn">
                                <strong>Question 3: &ldquo;Describe a challenge you overcame&rdquo;</strong>
                                Good story arc, but add a measurable outcome so recruiters see the impact of your actions.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p class="protected-trigger wow-section-cta">
                To use our model <a href="MockInterview.php" class="protected-trigger-link"<?= $toolCtaAuthAttr ?>>press here</a>.
            </p>
        </section>

            <!-- HOW IT WORKS -->
            <section class="how-it-works" id="how-it-works">
                <h2 class="section-title">How ElevUra AI Works</h2>

                <div class="steps-container">
                    <div class="step-card fade-in">
                        <h3>Upload or Input</h3>
                        
                        <p>Upload your CV, share your notes, or ask your question.</p>
                    </div>

                    <div class="step-card fade-in">
                        <h3>AI Processing</h3>
                        <p>Our AI analyzes, understands, and optimizes your content.</p>
                    </div>

                    <div class="step-card fade-in">
                        <h3>Smart Results</h3>
                        <p>Get actionable insights and improved outputs instantly.</p>
                    </div>
                </div>
                
            </section>
            <section class="metrics">
        <div class="metrics-container">
            <div class="metric-item">
                <h3>12,000+</h3>
                <p>CVs Optimized</p>
            </div>
            <div class="metric-item">
                <h3>89%</h3>
                <p>Interview Success</p>
            </div>
            <div class="metric-item">
                <h3>30,000+</h3>
                <p>Study Sessions</p>
            </div>
            <div class="metric-item">
                <h3>95%</h3>
                <p>User Satisfaction</p>
            </div>
        </div>
        
    </section>
    
 <!-- TESTIMONIALS -->
    <section class="testimonials">
        <h2 class="section-title">Student Success Stories</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card fade-in">
                <div class="stars" aria-label="5 out of 5 stars">5/5</div>
                <p class="testimonial-text">"ElevUra AI helped me improve my resume ATS score by 35 points. Got 3 interviews in one week!"</p>
                <div class="testimonial-author">- Emma, Engineering Student</div>
            </div>

            <div class="testimonial-card fade-in">
                <div class="stars" aria-label="5 out of 5 stars">5/5</div>
                <p class="testimonial-text">"Career Prep helped me understand complex algorithms and feel ready for technical interviews."</p>
                <div class="testimonial-author">- Liam, Computer Science</div>
            </div>

            <div class="testimonial-card fade-in">
                <div class="stars" aria-label="5 out of 5 stars">5/5</div>
                <p class="testimonial-text">"The Career Coach prepared me for interviews perfectly. I landed my dream job at a top tech company!"</p>
                <div class="testimonial-author">- Sarah, Business Graduate</div>
            </div>
        </div>
    </section>
    <!-- FAQ -->
    <section class="faq" id="faq">
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-container">
            <div class="faq-item active">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h4>How does ElevUra AI analyze my resume?</h4>
                    <div class="faq-toggle">v</div>
                </div>
                <div class="faq-answer">
                    ElevUra AI uses advanced NLP and machine learning to analyze your resume against industry standards, ATS systems, and job requirements. It provides actionable feedback on optimization.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h4>Is my data secure and private?</h4>
                    <div class="faq-toggle">v</div>
                </div>
                <div class="faq-answer">
                    Yes! We use enterprise-grade encryption and comply with GDPR and privacy regulations. Your data is never shared with third parties.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h4>Can I use ElevUra AI for multiple languages?</h4>
                    <div class="faq-toggle">v</div>
                </div>
                <div class="faq-answer">
                    Currently, we support English and Albanian. More languages are coming soon! Let us know which languages you'd like to see.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h4>What's included in the free plan?</h4>
                    <div class="faq-toggle">v</div>
                </div>
                <div class="faq-answer">
                    Free plan includes: 5 CV analyses/month, unlimited study materials, basic AI chat (5 conversations/day), and limited research assistance.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFAQ(this)">
                    <h4>How accurate is the Career Coach AI?</h4>
                    <div class="faq-toggle">v</div>
                </div>
                <div class="faq-answer">
                    Our AI is trained on thousands of successful career transitions and job placements. It provides insights with 90%+ accuracy based on market data.
                </div>
            </div>
        </div>
    </section>
            </section>

