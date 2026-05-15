            <header class="top-header">
                <div class="header-left">
                    <div class="header-left-meta">
                        <div class="environment-text">
                            Environment: <span class="production">Production</span>
                        </div>
                    </div>
                </div>

                <div class="header-right">
                    <div class="notification-icon" title="Notifications" aria-label="Notifications">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span class="notification-dot"></span>
                    </div>

                    <div class="header-auth" id="header-auth-guest"<?= $loggedIn ? ' hidden' : '' ?>>
                        <button type="button" class="header-auth-btn" data-auth-open="login">Login</button>
                        <button type="button" class="header-auth-btn header-auth-btn--primary" data-auth-open="signup">Sign up</button>
                    </div>

                    <div class="user-profile-wrap" id="header-auth-user"<?= $loggedIn ? '' : ' hidden' ?>>
                        <button type="button" class="user-info user-info--trigger" id="profile-trigger" aria-expanded="false" aria-haspopup="true" aria-controls="profile-dropdown">
                            <div class="user-avatar">
                                <img id="profile-avatar-img" src="<?= e($currentUser['avatar'] ?? '') ?>" width="36" height="36" alt="<?= e(($currentUser['username'] ?? 'User') . ' avatar') ?>" loading="lazy" decoding="async">
                            </div>
                            <div class="user-meta">
                                <div class="user-name" id="profile-username"><?= e($currentUser['username'] ?? 'User') ?></div>
                                <div class="user-tier" id="profile-tier"><?= e(($currentUser['tier'] ?? 'Free') . ' Tier') ?></div>
                            </div>
                            <span class="profile-chevron" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                            </span>
                        </button>
                        <div class="profile-dropdown" id="profile-dropdown" role="menu">
                            <button type="button" class="profile-dropdown__item" id="profile-open-dashboard" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></span>
                                My Dashboard
                            </button>
                            <button type="button" class="profile-dropdown__item" data-profile-action="nav" data-ud-section="cvs" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></span>
                                My CVs
                            </button>
                            <button type="button" class="profile-dropdown__item" data-profile-action="nav" data-ud-section="jobs" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></span>
                                Applied Jobs
                            </button>
                            <button type="button" class="profile-dropdown__item" data-profile-action="nav" data-ud-section="courses" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.1 2.7 3 6 3s6-1.9 6-3v-5"/></svg></span>
                                Courses Completed
                            </button>
                            <button type="button" class="profile-dropdown__item" data-profile-action="nav" data-ud-section="interviews" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2a3 3 0 0 0-3 3v4a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v1a7 7 0 0 1-14 0v-1"/><line x1="12" x2="12" y1="19" y2="22"/></svg></span>
                                Mock Interviews
                            </button>
                            <button type="button" class="profile-dropdown__item" data-profile-action="nav" data-ud-section="settings" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg></span>
                                Account Settings
                            </button>
                            <div class="profile-dropdown__divider" role="separator"></div>
                            <button type="button" class="profile-dropdown__item profile-dropdown__item--danger" data-profile-action="logout" role="menuitem">
                                <span class="profile-dropdown__icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg></span>
                                Logout
                            </button>
                        </div>
                    </div>

                    <button type="button" class="header-fullscreen" title="Fullscreen" aria-label="Toggle fullscreen">
                        <svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 0 0-2 2v3M21 8V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3M16 21h3a2 2 0 0 0 2-2v-3"/></svg>
                    </button>
                </div>
            </header>
