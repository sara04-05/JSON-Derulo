/**
 * ElevUra — protected tools: lock UI + gated navigation
 */
(function () {
  const TOOL_SELECTORS = [
    '.module-card[data-protected-tool]',
    '.sidebar-item[data-protected-tool]',
    '.study-buddy-card[data-protected-tool]',
    '.study-buddy-btn[data-protected-tool]',
    '.protected-trigger',
    '.execute-button[data-protected-tool]',
    '.command-input-wrapper[data-protected-tool]',
    '#btnStart[data-protected-tool]',
  ].join(',');

  function isLoggedIn() {
    return window.ElevUraAuth?.isLoggedIn();
  }

  function openAuth() {
    if (window.ElevUraAuthUI?.openAuth) {
      window.ElevUraAuthUI.openAuth('login');
    }
  }

  function handleProtectedAction(el, toolId) {
    if (isLoggedIn()) {
      if (toolId === 'mock-interview' || toolId === 'model') {
        window.ElevUraViews?.showCommandCenter();
        document.getElementById('mock-interview')?.scrollIntoView({ behavior: 'smooth' });
      } else if (toolId === 'career-coach' || toolId === 'cv-optimizer' || toolId === 'study-buddy' || toolId === 'research-assistant') {
        window.ElevUraViews?.showUserDashboard('overview');
        el.classList.add('protected-unlock-flash');
        setTimeout(() => el.classList.remove('protected-unlock-flash'), 600);
      } else if (toolId && window.ElevUraViews) {
        window.ElevUraViews.showUserDashboard('overview');
      } else {
        el.classList.add('protected-unlock-flash');
        setTimeout(() => el.classList.remove('protected-unlock-flash'), 600);
      }
      return true;
    }
    openAuth();
    return false;
  }

  function ensureLockOverlay(card) {
    if (card.querySelector('.protected-lock-overlay')) return;
    const overlay = document.createElement('div');
    overlay.className = 'protected-lock-overlay';
    overlay.innerHTML = `
      <span class="protected-lock-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      </span>
      <span class="protected-lock-badge">Login Required</span>
    `;
    card.appendChild(overlay);
  }

  function syncProtectedState() {
    const loggedIn = isLoggedIn();
    document.body.classList.toggle('tools-unlocked', loggedIn);

    document.querySelectorAll('[data-protected-tool]').forEach((el) => {
      el.classList.toggle('is-protected-locked', !loggedIn);
      el.classList.toggle('is-protected-unlocked', loggedIn);
      if (el.classList.contains('module-card') || el.classList.contains('study-buddy-card')) {
        if (!loggedIn) ensureLockOverlay(el);
        else el.querySelector('.protected-lock-overlay')?.remove();
      }
    });
  }

  function bindProtectedClicks() {
    document.querySelectorAll(TOOL_SELECTORS).forEach((el) => {
      if (el.dataset.protectedBound) return;
      el.dataset.protectedBound = '1';

      const toolId = el.getAttribute('data-protected-tool') || '';
      const tag = el.tagName.toLowerCase();

      if (tag === 'input') return;

      el.addEventListener('click', (e) => {
        if (!el.hasAttribute('data-protected-tool') && !el.classList.contains('protected-trigger')) return;
        if (isLoggedIn()) return;
        e.preventDefault();
        e.stopPropagation();
        openAuth();
      });

      if (el.classList.contains('protected-trigger')) {
        el.style.cursor = 'pointer';
        el.setAttribute('role', 'button');
        el.setAttribute('tabindex', '0');
        el.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            handleProtectedAction(el, toolId || 'model');
          }
        });
        el.addEventListener('click', (e) => {
          e.preventDefault();
          handleProtectedAction(el, toolId || 'model');
        });
      }
    });

    document.querySelectorAll('.sidebar-item[data-protected-tool]').forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        const tool = item.getAttribute('data-protected-tool');
        if (isLoggedIn()) {
          document.querySelectorAll('.sidebar-item').forEach((i) => i.classList.remove('active'));
          item.classList.add('active');
          if (window.ElevUraViews) window.ElevUraViews.showUserDashboard('overview', tool);
        } else {
          openAuth();
        }
      });
    });

    document.querySelectorAll('.module-card[data-protected-tool]').forEach((card) => {
      card.addEventListener('click', (e) => {
        if (!isLoggedIn()) {
          e.preventDefault();
          openAuth();
          return;
        }
        const tool = card.getAttribute('data-protected-tool');
        handleProtectedAction(card, tool);
      });
    });
  }

  function init() {
    syncProtectedState();
    bindProtectedClicks();
    window.addEventListener('elevura:auth-change', syncProtectedState);
  }

  window.ElevUraProtected = { syncProtectedState, handleProtectedAction };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
