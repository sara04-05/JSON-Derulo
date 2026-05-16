<?php
/** @var string $activeNav home|dashboard|research-assistant */
$activeNav = $activeNav ?? ($pageSlug === 'user-dashboard' ? 'dashboard' : ($pageSlug === 'research-assistant' ? 'research-assistant' : 'home'));
$sidebarGuest = empty($loggedIn);
$sidebarAuthAttr = $sidebarGuest ? ' data-auth-open="login"' : '';
?>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <span class="sidebar-brand-text">ElevUra</span>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php" class="sidebar-item<?= $activeNav === 'home' ? ' active' : '' ?>" data-view="command"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon sidebar-item-icon-terminal" aria-hidden="true">&gt;_</span>
                    <span>Command Center</span>
                </a>
                <a href="user_dashboard.php" class="sidebar-item<?= $activeNav === 'dashboard' ? ' active' : '' ?>" id="sidebar-mission-control" data-sidebar-dashboard<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    </span>
                    <span>Mission Control</span>
                </a>
                <a href="index.php#module-career-coach" class="sidebar-item"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <span>AI Career Coach</span>
                </a>
                <a href="index.php#module-cv-optimizer" class="sidebar-item"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                    </span>
                    <span>CV Optimizer</span>
                </a>
                <a href="study-buddy.php" class="sidebar-item" data-nav-tool="study-buddy">
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="9" cy="12" r="5"/><circle cx="15" cy="12" r="5"/></svg>
                    </span>
                    <span>Study Buddy</span>
                </a>
                <a href="research_assistant.php" class="sidebar-item<?= $activeNav === 'research-assistant' ? ' active' : '' ?>">
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </span>
                    <span>AI CV Writer</span>
                </a>
            </nav>

            <div class="sidebar-auth" id="cw9j21"<?= $loggedIn ? ' hidden' : '' ?>>
                <button type="button" class="sidebar-auth-btn" id="auth-open-login" aria-haspopup="dialog" aria-controls="auth-modal-root">
                    <span class="sidebar-auth-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
                    </span>
                    <span class="sidebar-auth-label">Login</span>
                </button>
                <button type="button" class="sidebar-auth-btn sidebar-auth-btn--primary" id="auth-open-signup" aria-haspopup="dialog" aria-controls="auth-modal-root">
                    <span class="sidebar-auth-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                    </span>
                    <span class="sidebar-auth-label">Sign up</span>
                </button>
            </div>
        </aside>
