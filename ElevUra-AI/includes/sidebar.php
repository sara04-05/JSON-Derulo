<?php
/** @var string $activeNav home|dashboard|research-assistant|study-buddy */
$activeNav = $activeNav ?? (
    $pageSlug === 'user-dashboard' ? 'dashboard' : (
        $pageSlug === 'research-assistant' ? 'research-assistant' : (
            $pageSlug === 'study-buddy' ? 'study-buddy' : 'home'
        )
    )
);
$sidebarGuest = empty($loggedIn);
$sidebarAuthAttr = $sidebarGuest ? ' data-auth-open="login"' : '';

$navItems = [
    'home' => ['href' => 'index.php', 'label' => 'Command Center', 'icon' => 'terminal'],
    'career-coach' => ['href' => 'MockInterview.php', 'label' => 'AI Career Coach', 'icon' => 'coach'],
    'cv-optimizer' => ['href' => 'cv-optimizer.php', 'label' => 'CV Optimizer', 'icon' => 'cv'],
    'ai-cv-writer' => ['href' => 'CVwriter.php', 'label' => 'AI CV Writer', 'icon' => 'writer'],
    'study-buddy' => ['href' => 'study-buddy.php', 'label' => 'Career Prep', 'icon' => 'study'],
    'dashboard' => ['href' => 'user_dashboard.php', 'label' => 'Mission Control', 'icon' => 'grid'],
];

function sidebar_active(string $key, string $activeNav): string
{
    return $key === $activeNav ? ' active' : '';
}
?>
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                </div>
                <span class="sidebar-brand-text">ElevUra</span>
            </div>
            <nav class="sidebar-menu">
                <a href="<?= e($navItems['home']['href']) ?>" class="sidebar-item<?= sidebar_active('home', $activeNav) ?>" data-view="command"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon sidebar-item-icon-terminal" aria-hidden="true">&gt;_</span>
                    <span><?= e($navItems['home']['label']) ?></span>
                </a>
                <a href="<?= e($navItems['career-coach']['href']) ?>" class="sidebar-item<?= sidebar_active('career-coach', $activeNav) ?>"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <span><?= e($navItems['career-coach']['label']) ?></span>
                </a>
                <a href="<?= e($navItems['cv-optimizer']['href']) ?>" class="sidebar-item<?= sidebar_active('cv-optimizer', $activeNav) ?>"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                    </span>
                    <span><?= e($navItems['cv-optimizer']['label']) ?></span>
                </a>
                <a href="<?= e($navItems['ai-cv-writer']['href']) ?>" class="sidebar-item<?= sidebar_active('ai-cv-writer', $activeNav) ?>"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </span>
                    <span><?= e($navItems['ai-cv-writer']['label']) ?></span>
                </a>
                <a href="<?= e($navItems['study-buddy']['href']) ?>" class="sidebar-item<?= sidebar_active('study-buddy', $activeNav) ?>"<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="9" cy="12" r="5"/><circle cx="15" cy="12" r="5"/></svg>
                    </span>
                    <span><?= e($navItems['study-buddy']['label']) ?></span>
                </a>
                <a href="<?= e($navItems['dashboard']['href']) ?>" class="sidebar-item<?= sidebar_active('dashboard', $activeNav) ?>" id="sidebar-mission-control" data-sidebar-dashboard<?= $sidebarAuthAttr ?>>
                    <span class="sidebar-item-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                    </span>
                    <span><?= e($navItems['dashboard']['label']) ?></span>
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
