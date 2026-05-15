<?php
$udCvs = count($dashboardData['cvs'] ?? []);
$udJobs = count($dashboardData['applied_jobs'] ?? []);
$udCourses = count($dashboardData['courses'] ?? []);
$udOverall = (int) ($dashboardData['analytics']['overall_score'] ?? 0);
$udUsername = e($currentUser['username'] ?? 'User');
$udTier = e($currentUser['tier'] ?? 'Free');
?>
            <section class="content-area ud-content" id="view-user-dashboard">
                <div class="ud-bg-effects" aria-hidden="true">
                    <span class="ud-orb ud-orb--cyan"></span>
                    <span class="ud-orb ud-orb--purple"></span>
                </div>

                <div class="ud-shell">
                    <header class="ud-hero-panel">
                        <span class="ud-corner ud-corner--tl" aria-hidden="true"></span>
                        <span class="ud-corner ud-corner--br" aria-hidden="true"></span>
                        <div class="ud-hero-shimmer" aria-hidden="true"></div>

                        <div class="ud-hero-top">
                            <div class="ud-hero-copy">
                                <div class="hero-badge">&gt; MISSION CONTROL ONLINE</div>
                                <h1 class="hero-title ud-hero-title">Welcome back, <span class="hero-title-gradient"><?= $udUsername ?></span></h1>
                                <p class="hero-subtitle">Track CVs, applications, learning progress, and interview analytics — all synced to your ElevUra profile.</p>
                                <div class="ud-hero-meta">
                                    <span class="ud-tier-pill"><?= $udTier ?> Tier</span>
                                    <span class="ud-sync-badge">Live sync active</span>
                                </div>
                            </div>

                            <div class="ud-quick-stats" aria-label="Dashboard summary">
                                <article class="ud-quick-stat" data-stat="cvs">
                                    <span class="ud-quick-stat__accent" aria-hidden="true"></span>
                                    <span class="ud-quick-stat__value" id="ud-stat-cvs"><?= $udCvs ?></span>
                                    <span class="ud-quick-stat__label">CVs uploaded</span>
                                </article>
                                <article class="ud-quick-stat" data-stat="jobs">
                                    <span class="ud-quick-stat__accent" aria-hidden="true"></span>
                                    <span class="ud-quick-stat__value" id="ud-stat-jobs"><?= $udJobs ?></span>
                                    <span class="ud-quick-stat__label">Applications</span>
                                </article>
                                <article class="ud-quick-stat" data-stat="courses">
                                    <span class="ud-quick-stat__accent" aria-hidden="true"></span>
                                    <span class="ud-quick-stat__value" id="ud-stat-courses"><?= $udCourses ?></span>
                                    <span class="ud-quick-stat__label">Courses</span>
                                </article>
                                <article class="ud-quick-stat ud-quick-stat--accent" data-stat="score">
                                    <span class="ud-quick-stat__accent" aria-hidden="true"></span>
                                    <span class="ud-quick-stat__value" id="ud-stat-score"><?= $udOverall ?></span>
                                    <span class="ud-quick-stat__label">Latest interview score</span>
                                </article>
                            </div>
                        </div>

                        <nav class="ud-nav" aria-label="Dashboard sections">
                            <a href="#my-cvs" class="ud-nav-btn is-active" data-ud-nav="cvs">
                                <span class="ud-nav-btn__dot" aria-hidden="true"></span>
                                My CVs
                            </a>
                            <a href="#applied-jobs" class="ud-nav-btn" data-ud-nav="jobs">
                                <span class="ud-nav-btn__dot" aria-hidden="true"></span>
                                Applied Jobs
                            </a>
                            <a href="#courses-completed" class="ud-nav-btn" data-ud-nav="courses">
                                <span class="ud-nav-btn__dot" aria-hidden="true"></span>
                                Courses
                            </a>
                            <a href="#mock-interviews" class="ud-nav-btn" data-ud-nav="interviews">
                                <span class="ud-nav-btn__dot" aria-hidden="true"></span>
                                Mock Interviews
                            </a>
                        </nav>
                    </header>

                    <div class="ud-dashboard-grid">
                        <section class="ud-panel ud-panel--half" id="my-cvs">
                            <div class="ud-section-head">
                                <div class="ud-section-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                                </div>
                                <div>
                                    <h2 class="ud-section-title">My <span>CVs</span></h2>
                                    <p class="ud-section-desc">ATS-optimized documents with live scoring.</p>
                                </div>
                            </div>
                            <div class="ud-panel-body">
                                <div class="ud-grid" id="ud-cvs-grid"></div>
                            </div>
                        </section>

                        <section class="ud-panel ud-panel--half" id="applied-jobs">
                            <div class="ud-section-head">
                                <div class="ud-section-icon ud-section-icon--purple" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                </div>
                                <div>
                                    <h2 class="ud-section-title">Applied <span>Jobs</span></h2>
                                    <p class="ud-section-desc">Pipeline status across your active applications.</p>
                                </div>
                            </div>
                            <div class="ud-panel-body">
                                <div class="ud-grid ud-grid--jobs" id="ud-jobs-grid"></div>
                            </div>
                        </section>

                        <section class="ud-panel ud-panel--full" id="courses-completed">
                            <div class="ud-section-head">
                                <div class="ud-section-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg>
                                </div>
                                <div>
                                    <h2 class="ud-section-title">Courses <span>Completed</span></h2>
                                    <p class="ud-section-desc">Learning velocity and completion analytics.</p>
                                </div>
                            </div>
                            <div class="ud-panel-body">
                                <div class="ud-grid ud-grid--courses" id="ud-courses-grid"></div>
                            </div>
                        </section>

                        <section class="ud-panel ud-panel--featured" id="mock-interviews">
                            <div class="ud-section-head ud-section-head--featured">
                                <div class="ud-section-icon ud-section-icon--featured" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M12 2a3 3 0 0 0-3 3v4a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v1a7 7 0 0 1-14 0v-1"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                                </div>
                                <div>
                                    <h2 class="ud-section-title">Mock Interview <span>Analytics</span></h2>
                                    <p class="ud-section-desc">AI performance insights across your practice sessions.</p>
                                </div>
                            </div>

                            <div class="ud-panel-body">
                                <div class="ud-analytics-panel">
                                    <div class="ud-analytics-grid">
                                        <article class="ud-card ud-stat-card ud-stat-card--chart">
                                            <p class="ud-card-meta">Overall score</p>
                                            <div class="ud-sparkline" id="ud-sparkline" aria-hidden="true">
                                                <span style="--h:20%"></span><span style="--h:35%"></span><span style="--h:45%"></span><span style="--h:40%"></span><span style="--h:55%"></span><span style="--h:65%"></span><span style="--h:50%"></span>
                                            </div>
                                            <p class="ud-stat-value" id="ud-overall-score">0</p>
                                        </article>
                                        <article class="ud-card ud-stat-card">
                                            <p class="ud-card-meta">Communication</p>
                                            <div class="ud-progress ud-progress--glow"><div class="ud-progress__bar" id="ud-comm-bar" style="--progress: 0%"></div></div>
                                            <p class="ud-stat-value ud-stat-value--sm" id="ud-comm-score">0%</p>
                                        </article>
                                        <article class="ud-card ud-stat-card">
                                            <p class="ud-card-meta">Confidence</p>
                                            <div class="ud-progress ud-progress--glow"><div class="ud-progress__bar ud-progress__bar--purple" id="ud-conf-bar" style="--progress: 0%"></div></div>
                                            <p class="ud-stat-value ud-stat-value--sm" id="ud-conf-score">0%</p>
                                        </article>
                                        <article class="ud-card ud-stat-card ud-stat-card--wide">
                                            <p class="ud-card-meta">AI feedback summary</p>
                                            <p class="ud-feedback-text" id="ud-feedback-text">Complete a mock interview on the Command Center to receive AI feedback.</p>
                                        </article>
                                    </div>
                                </div>

                                <div class="ud-sessions-block">
                                    <div class="ud-section-head ud-section-head--sub">
                                        <h3 class="ud-subtitle">Recent sessions</h3>
                                        <span class="ud-section-divider" aria-hidden="true"></span>
                                    </div>
                                    <div class="ud-grid ud-grid--interviews" id="ud-interviews-grid"></div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </section>
