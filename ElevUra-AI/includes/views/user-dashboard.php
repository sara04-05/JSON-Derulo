            <section class="content-area ud-content" id="view-user-dashboard" hidden>
                <div class="ud-hero-panel">
                    <div class="hero-badge">&gt; USER DASHBOARD ONLINE</div>
                    <h1 class="hero-title">Your <span class="hero-title-gradient">Mission Control</span></h1>
                    <p class="hero-subtitle">Track CVs, applications, learning progress, and interview analytics â€” all synchronized to your ElevUra profile.</p>
                    <nav class="ud-nav" aria-label="Dashboard sections">
                        <button type="button" class="ud-nav-btn is-active" data-ud-nav="overview">Overview</button>
                        <button type="button" class="ud-nav-btn" data-ud-nav="cvs">My CVs</button>
                        <button type="button" class="ud-nav-btn" data-ud-nav="jobs">Applied Jobs</button>
                        <button type="button" class="ud-nav-btn" data-ud-nav="courses">Courses</button>
                        <button type="button" class="ud-nav-btn" data-ud-nav="interviews">Mock Interviews</button>
                        <button type="button" class="ud-nav-btn" data-ud-nav="settings">Settings</button>
                    </nav>
                </div>

                <section class="ud-section" id="ud-section-cvs">
                    <div class="ud-section-head">
                        <h2 class="section-title">My <span>CVs</span></h2>
                        <p class="ud-section-desc">ATS-optimized documents with live scoring.</p>
                    </div>
                    <div class="ud-grid" id="ud-cvs-grid"></div>
                </section>

                <section class="ud-section" id="ud-section-jobs">
                    <div class="ud-section-head">
                        <h2 class="section-title">Applied <span>Jobs</span></h2>
                        <p class="ud-section-desc">Pipeline status across your active applications.</p>
                    </div>
                    <div class="ud-grid ud-grid--jobs" id="ud-jobs-grid"></div>
                </section>

                <section class="ud-section" id="ud-section-courses">
                    <div class="ud-section-head">
                        <h2 class="section-title">Courses <span>Completed</span></h2>
                        <p class="ud-section-desc">Learning velocity and completion analytics.</p>
                    </div>
                    <div class="ud-grid" id="ud-courses-grid"></div>
                </section>

                <section class="ud-section" id="ud-section-interviews">
                    <div class="ud-section-head">
                        <h2 class="section-title">Mock Interview <span>Analytics</span></h2>
                        <p class="ud-section-desc">AI performance insights across your practice sessions.</p>
                    </div>
                    <div class="ud-analytics-grid">
                        <article class="ud-card ud-stat-card">
                            <p class="ud-card-meta">Overall score trend</p>
                            <div class="ud-sparkline" aria-hidden="true">
                                <span style="--h:42%"></span><span style="--h:55%"></span><span style="--h:61%"></span><span style="--h:58%"></span><span style="--h:72%"></span><span style="--h:78%"></span><span style="--h:87%"></span>
                            </div>
                            <p class="ud-stat-value">87 <span class="ud-stat-delta ud-stat-delta--up">+12%</span></p>
                        </article>
                        <article class="ud-card ud-stat-card">
                            <p class="ud-card-meta">Communication</p>
                            <div class="ud-progress"><div class="ud-progress__bar" style="--progress: 88%"></div></div>
                            <p class="ud-stat-value">88%</p>
                        </article>
                        <article class="ud-card ud-stat-card">
                            <p class="ud-card-meta">Confidence</p>
                            <div class="ud-progress"><div class="ud-progress__bar ud-progress__bar--purple" style="--progress: 92%"></div></div>
                            <p class="ud-stat-value">92%</p>
                        </article>
                        <article class="ud-card ud-stat-card ud-stat-card--wide">
                            <p class="ud-card-meta">AI feedback summary</p>
                            <p class="ud-feedback-text">Strong structure in behavioral answers. Reduce filler words in technical explanations. Lead with metrics when discussing project impact.</p>
                            <div class="ud-tags">
                                <span class="ud-tag ud-tag--good">Clear storytelling</span>
                                <span class="ud-tag ud-tag--good">Role alignment</span>
                                <span class="ud-tag ud-tag--warn">Pacing under pressure</span>
                                <span class="ud-tag ud-tag--warn">Technical depth</span>
                            </div>
                        </article>
                    </div>
                </section>

                <section class="ud-section" id="ud-section-settings">
                    <div class="ud-section-head">
                        <h2 class="section-title">Account <span>Settings</span></h2>
                        <p class="ud-section-desc">Your ElevUra profile synced from the database.</p>
                    </div>
                    <article class="ud-card ud-settings-card">
                        <div class="field">
                            <label>Username</label>
                            <input type="text" id="settings-username" readonly autocomplete="username" value="<?= e($currentUser['username'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" id="settings-email" readonly autocomplete="email" value="<?= e($currentUser['email'] ?? '') ?>">
                        </div>
                        <div class="field">
                            <label>Membership tier</label>
                            <input type="text" id="settings-tier" readonly value="<?= e($currentUser['tier'] ?? 'Free') ?>">
                        </div>
                    </article>
                </section>
            </section>
