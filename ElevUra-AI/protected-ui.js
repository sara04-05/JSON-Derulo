/**
 * ElevUra — protected tools: lock UI + gated navigation (homepage only)
 */
(function () {
  const LOCKABLE_SELECTOR = [
    '[data-protected-block]',
    '.module-card[data-protected-tool]',
    '.study-buddy-card[data-protected-tool]',
    '.study-buddy-btn[data-protected-tool]',
    '#btnStart[data-protected-tool]',
  ].join(',');

  const CLICK_SELECTOR = [
    LOCKABLE_SELECTOR,
    '.protected-trigger[data-protected-tool]',
  ].join(',');

  function isHomePage() {
    return document.body.dataset.page === 'home';
  }

  function isLoggedIn() {
    return window.ElevUraAuth?.isLoggedIn();
  }

  function openAuth() {
    window.ElevUraAuthUI?.openAuth('login');
  }

  function ensureLockOverlay(el) {
    if (el.querySelector('.protected-lock-overlay')) return;
    const overlay = document.createElement('div');
    overlay.className = 'protected-lock-overlay';
    overlay.innerHTML = `
      <span class="protected-lock-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      </span>
      <span class="protected-lock-badge">Login Required</span>
    `;
    el.appendChild(overlay);
  }

  function syncProtectedState() {
    if (!isHomePage()) return;

    const loggedIn = isLoggedIn();
    document.body.classList.toggle('tools-unlocked', loggedIn);

    document.querySelectorAll(LOCKABLE_SELECTOR).forEach((el) => {
      el.classList.toggle('is-protected-locked', !loggedIn);
      el.classList.toggle('is-protected-unlocked', loggedIn);
      if (!loggedIn) ensureLockOverlay(el);
      else el.querySelector('.protected-lock-overlay')?.remove();
    });
  }

  function handleProtectedClick(e, el) {
    if (isLoggedIn()) {
      const block = el.getAttribute('data-protected-block');
      if (block === 'mock-interview') {
        document.getElementById('mock-interview')?.scrollIntoView({ behavior: 'smooth' });
      } else if (block === 'cv-scoring') {
        document.getElementById('cv-scoring-section')?.scrollIntoView({ behavior: 'smooth' });
      }
      return;
    }
    e.preventDefault();
    e.stopPropagation();
    openAuth();
  }

  function bindProtectedClicks() {
    if (!isHomePage()) return;

    document.querySelectorAll(CLICK_SELECTOR).forEach((el) => {
      if (el.dataset.protectedBound) return;
      el.dataset.protectedBound = '1';

      el.addEventListener('click', (e) => {
        if (isLoggedIn()) return;
        handleProtectedClick(e, el);
      });
    });

    document.querySelectorAll('.protected-trigger').forEach((el) => {
      if (el.dataset.protectedBound) return;
      el.dataset.protectedBound = '1';
      el.style.cursor = 'pointer';
      el.setAttribute('role', 'button');
      el.setAttribute('tabindex', '0');
      el.addEventListener('click', (e) => handleProtectedClick(e, el));
      el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          handleProtectedClick(e, el);
        }
      });
    });

    document.querySelectorAll('.module-card[data-protected-tool]').forEach((card) => {
      card.addEventListener('click', (e) => {
        if (isLoggedIn()) return;
        handleProtectedClick(e, card);
      });
    });
  }

  function bindSidebarNav() {
    document.querySelector('[data-sidebar-dashboard]')?.addEventListener('click', (e) => {
      if (!isLoggedIn()) {
        e.preventDefault();
        openAuth();
      }
    });

    document.querySelectorAll('[data-nav-tool]').forEach((link) => {
      link.addEventListener('click', (e) => {
        if (isLoggedIn()) return;
        e.preventDefault();
        openAuth();
      });
    });
  }

  function init() {
    syncProtectedState();
    bindProtectedClicks();
    bindSidebarNav();
    window.addEventListener('elevura:auth-change', syncProtectedState);
  }

  window.ElevUraProtected = { syncProtectedState };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
