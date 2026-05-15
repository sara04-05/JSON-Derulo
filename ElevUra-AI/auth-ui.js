/**
 * ElevUra — auth modal, header profile, dropdown, UI sync
 */
(function () {
  const AUTH_EVENT = 'elevura:auth-change';

  function $(sel, root) {
    return (root || document).querySelector(sel);
  }

  function showToast(message) {
    let el = document.getElementById('auth-toast');
    if (!el) {
      el = document.createElement('div');
      el.id = 'auth-toast';
      el.className = 'auth-toast';
      el.setAttribute('role', 'status');
      document.body.appendChild(el);
    }
    el.textContent = message;
    el.classList.add('is-visible');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => el.classList.remove('is-visible'), 3200);
  }

  function syncAuthChrome() {
    const user = window.ElevUraAuth?.getUser();
    const loggedIn = !!user;
    document.body.classList.toggle('is-logged-in', loggedIn);
    document.body.classList.toggle('is-logged-out', !loggedIn);

    const guest = $('#header-auth-guest');
    const profileWrap = $('#header-auth-user');
    const sidebarAuth = $('#cw9j21');
    const sidebarDash = $('#sidebar-my-dashboard');

    if (guest) guest.hidden = loggedIn;
    if (profileWrap) profileWrap.hidden = !loggedIn;
    if (sidebarAuth) sidebarAuth.hidden = loggedIn;
    if (sidebarDash) sidebarDash.hidden = !loggedIn;

    if (loggedIn && user) {
      const nameEl = $('#profile-username');
      const tierEl = $('#profile-tier');
      const imgEl = $('#profile-avatar-img');
      if (nameEl) nameEl.textContent = user.username;
      if (tierEl) tierEl.textContent = `${user.tier} Tier`;
      if (imgEl) {
        imgEl.src = user.avatar;
        imgEl.alt = `${user.username} avatar`;
      }
    }
  }

  function initProfileDropdown() {
    const trigger = $('#profile-trigger');
    const menu = $('#profile-dropdown');
    if (!trigger || !menu) return;

    const open = () => {
      menu.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
      menu.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
    };

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      if (menu.classList.contains('is-open')) close();
      else open();
    });

    menu.querySelectorAll('[data-profile-action], #profile-open-dashboard').forEach((item) => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        close();
        const action = item.getAttribute('data-profile-action');
        if (action === 'logout') {
          window.ElevUraAuth.logout();
          showToast('Signed out successfully.');
          window.ElevUraViews?.showCommandCenter();
          return;
        }
        if (action === 'nav' || item.id === 'profile-open-dashboard') {
          const section = item.getAttribute('data-ud-section') || 'overview';
          window.ElevUraViews?.showUserDashboard(section);
        }
      });
    });

    document.addEventListener('click', (e) => {
      if (!menu.classList.contains('is-open')) return;
      if (menu.contains(e.target) || trigger.contains(e.target)) return;
      close();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  }

  function initAuthModal() {
    const root = document.getElementById('auth-modal-root');
    if (!root) return;

    const dialog = root.querySelector('.auth-modal__dialog');
    const closeBtn = document.getElementById('auth-modal-close');
    const tabLogin = document.getElementById('auth-tab-login');
    const tabSignup = document.getElementById('auth-tab-signup');
    const panelLogin = document.getElementById('auth-panel-login');
    const panelSignup = document.getElementById('auth-panel-signup');
    const subtitle = document.getElementById('auth-modal-subtitle');
    const flash = document.getElementById('auth-modal-flash');
    const formLogin = document.getElementById('auth-form-login');
    const formSignup = document.getElementById('auth-form-signup');
    const btnLogin = document.getElementById('auth-btn-login');
    const btnSignup = document.getElementById('auth-btn-signup');

    let lastFocus = null;
    let closeTimer = null;

    const isValidEmail = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);

    const setFieldState = (wrap, msgEl, message, kind) => {
      if (!wrap) return;
      wrap.classList.remove('is-invalid', 'is-success');
      if (msgEl) {
        msgEl.textContent = message || '';
        msgEl.classList.remove('auth-field-msg--error', 'auth-field-msg--ok');
      }
      if (kind === 'error') {
        wrap.classList.add('is-invalid');
        if (msgEl) msgEl.classList.add('auth-field-msg--error');
      } else if (kind === 'ok') {
        wrap.classList.add('is-success');
        if (msgEl) msgEl.classList.add('auth-field-msg--ok');
      }
    };

    const setFlash = (text) => {
      if (flash) flash.textContent = text || '';
    };

    const scorePassword = (pw) => {
      let score = 0;
      if (pw.length >= 8) score++;
      if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
      if (/\d/.test(pw)) score++;
      if (/[^A-Za-z0-9]/.test(pw)) score++;
      return score;
    };

    const updateStrength = () => {
      const input = document.getElementById('auth-signup-password');
      const label = document.getElementById('auth-strength-label');
      if (!input || !label) return;
      const pw = input.value;
      const s = scorePassword(pw);
      root.querySelectorAll('#auth-strength-bars .auth-strength-bar').forEach((bar, i) => {
        bar.classList.remove('is-on', 'is-warn', 'is-bad');
        if (i < s) {
          bar.classList.add('is-on');
          if (s <= 2) bar.classList.add('is-bad');
          else if (s === 3) bar.classList.add('is-warn');
        }
      });
      if (!pw) label.textContent = 'Password strength: enter a password';
      else if (s <= 1) label.textContent = 'Password strength: weak';
      else if (s === 2) label.textContent = 'Password strength: fair';
      else if (s === 3) label.textContent = 'Password strength: good';
      else label.textContent = 'Password strength: excellent';
    };

    const wireToggle = (btnId, inputId) => {
      const btn = document.getElementById(btnId);
      const input = document.getElementById(inputId);
      if (!btn || !input) return;
      const eye = btn.querySelector('.auth-icon-eye');
      const eyeOff = btn.querySelector('.auth-icon-eye-off');
      btn.addEventListener('click', () => {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.setAttribute('aria-pressed', show ? 'true' : 'false');
        if (eye && eyeOff) {
          eye.style.display = show ? 'none' : 'block';
          eyeOff.style.display = show ? 'block' : 'none';
        }
      });
    };

    wireToggle('auth-toggle-login-pass', 'auth-login-password');
    wireToggle('auth-toggle-signup-pass', 'auth-signup-password');
    wireToggle('auth-toggle-signup-confirm', 'auth-signup-confirm');

    const setTab = (mode) => {
      const login = mode === 'login';
      tabLogin?.setAttribute('aria-selected', login ? 'true' : 'false');
      tabSignup?.setAttribute('aria-selected', login ? 'false' : 'true');
      if (tabLogin) tabLogin.tabIndex = login ? 0 : -1;
      if (tabSignup) tabSignup.tabIndex = login ? -1 : 0;
      panelLogin?.classList.toggle('is-active', login);
      panelSignup?.classList.toggle('is-active', !login);
      if (subtitle) subtitle.textContent = login ? 'Sign in to continue' : 'Create your account';
      const focusEl = login
        ? document.getElementById('auth-login-email')
        : document.getElementById('auth-signup-name');
      if (focusEl) setTimeout(() => focusEl.focus(), 50);
    };

    tabLogin?.addEventListener('click', () => setTab('login'));
    tabSignup?.addEventListener('click', () => setTab('signup'));

    const openAuth = (mode) => {
      lastFocus = document.activeElement;
      root.removeAttribute('hidden');
      root.setAttribute('aria-hidden', 'false');
      document.body.classList.add('auth-modal-open');
      setFlash('');
      requestAnimationFrame(() => {
        requestAnimationFrame(() => root.classList.add('is-open'));
      });
      setTab(mode === 'signup' ? 'signup' : 'login');
    };

    const closeAuth = () => {
      root.classList.remove('is-open');
      clearTimeout(closeTimer);
      closeTimer = setTimeout(() => {
        root.setAttribute('hidden', '');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('auth-modal-open');
        setFlash('');
        if (lastFocus?.focus) lastFocus.focus();
      }, 320);
    };

    window.ElevUraAuthUI = { openAuth, closeAuth };

    document.getElementById('auth-open-login')?.addEventListener('click', (e) => {
      e.preventDefault();
      openAuth('login');
    });
    document.getElementById('auth-open-signup')?.addEventListener('click', (e) => {
      e.preventDefault();
      openAuth('signup');
    });
    document.querySelectorAll('[data-auth-open]').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        openAuth(btn.getAttribute('data-auth-open') || 'login');
      });
    });

    closeBtn?.addEventListener('click', () => closeAuth());
    root.querySelectorAll('[data-auth-close]').forEach((el) => {
      el.addEventListener('click', () => closeAuth());
    });

    document.addEventListener('keydown', (e) => {
      if (!root.classList.contains('is-open')) return;
      if (e.key === 'Escape') {
        e.preventDefault();
        closeAuth();
      }
    });

    const loginEmail = document.getElementById('auth-login-email');
    const loginPass = document.getElementById('auth-login-password');
    const signupName = document.getElementById('auth-signup-name');
    const signupEmail = document.getElementById('auth-signup-email');
    const signupPass = document.getElementById('auth-signup-password');
    const signupConfirm = document.getElementById('auth-signup-confirm');
    const terms = document.getElementById('auth-signup-terms');

    signupPass?.addEventListener('input', updateStrength);

    document.getElementById('auth-forgot')?.addEventListener('click', () => {
      setFlash('If an account exists, a reset link was sent (demo).');
    });

    const submitWithAuth = (btn, buildUser, successMsg) => {
      if (!btn || btn.classList.contains('is-loading')) return;
      btn.classList.add('is-loading');
      btn.disabled = true;
      setTimeout(() => {
        const user = window.ElevUraAuth.setUser(buildUser());
        btn.classList.remove('is-loading');
        btn.disabled = false;
        syncAuthChrome();
        closeAuth();
        showToast(successMsg.replace('{name}', user?.username || 'there'));
      }, 900);
    };

    formLogin?.addEventListener('submit', (e) => {
      e.preventDefault();
      const email = loginEmail?.value.trim() || '';
      const passOk = (loginPass?.value.length || 0) >= 6;
      if (!isValidEmail(email)) {
        setFieldState(
          document.getElementById('auth-wrap-login-email'),
          document.getElementById('auth-login-email-msg'),
          'Enter a valid email.',
          'error'
        );
        loginEmail?.focus();
        return;
      }
      if (!passOk) {
        setFieldState(
          document.getElementById('auth-wrap-login-password'),
          document.getElementById('auth-login-password-msg'),
          'Check your password.',
          'error'
        );
        loginPass?.focus();
        return;
      }
      submitWithAuth(
        btnLogin,
        () => window.ElevUraAuth.userFromLogin(email),
        'Welcome back, {name}. Systems online.'
      );
    });

    formSignup?.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = signupName?.value.trim() || '';
      const email = signupEmail?.value.trim() || '';
      const pw = signupPass?.value || '';
      const conf = signupConfirm?.value || '';
      if (name.length < 2 || !isValidEmail(email) || pw.length < 8 || pw !== conf) {
        setFlash('Please complete all fields correctly.');
        return;
      }
      if (terms && !terms.checked) {
        setFlash('Accept the terms to continue.');
        return;
      }
      submitWithAuth(
        btnSignup,
        () => window.ElevUraAuth.userFromSignup(name, email),
        'Account initialized. Welcome, {name}.'
      );
    });
  }

  function init() {
    syncAuthChrome();
    initProfileDropdown();
    initAuthModal();
    window.addEventListener(AUTH_EVENT, syncAuthChrome);
    const user = window.ElevUraAuth?.getUser();
    const settingsName = document.getElementById('settings-username');
    const settingsTier = document.getElementById('settings-tier');
    if (user && settingsName) settingsName.value = user.username;
    if (user && settingsTier) settingsTier.value = user.tier;
    const settingsSave = document.getElementById('settings-save-demo');
    settingsSave?.addEventListener('click', () => {
      const user = window.ElevUraAuth.getUser();
      if (!user) return;
      const name = settingsName?.value.trim();
      if (name) {
        window.ElevUraAuth.setUser({ ...user, username: name.split(/\s+/)[0] });
        syncAuthChrome();
        showToast('Preferences saved (demo).');
      }
    });
    window.addEventListener('elevura:auth-change', () => {
      const user = window.ElevUraAuth.getUser();
      if (settingsName && user) settingsName.value = user.username;
      if (settingsTier && user) settingsTier.value = user.tier;
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
