/**
 * ElevUra Dashboard - Dashboard Interactions
 * Handles user interactions and command execution
 */

class ElevUraDashboard {
  constructor() {
    this.init();
    this.setupInteractions();
    this.setupMetricsAnimation();
    this.setupAuthModal();
  }

  init() {
    this.commandInput = document.querySelector('.command-input');
    this.executeButton = document.querySelector('.execute-button');
  }

  /**
   * Setup interactive behaviors
   */
  setupInteractions() {
    // Sidebar navigation
    this.setupSidebarNavigation();

    // Command input
    if (this.commandInput) {
      this.commandInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          this.executeCommand();
        }
      });
    }

    // Execute button
    if (this.executeButton) {
      this.executeButton.addEventListener('click', () => {
        this.executeCommand();
      });
    }

    // Other UI elements
    this.setupNotificationIcon();
    this.setupFullscreenToggle();
  }

  /**
   * Setup sidebar navigation
   */
  setupSidebarNavigation() {
    document.querySelectorAll('.sidebar-menu .sidebar-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');
      });
    });
  }

  /**
   * Setup fullscreen toggle
   */
  setupFullscreenToggle() {
    const btn = document.querySelector('.header-fullscreen');
    if (!btn) return;
    
    btn.addEventListener('click', () => {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen?.().catch(() => {});
      } else {
        document.exitFullscreen?.().catch(() => {});
      }
    });
  }

  /**
   * Setup notification icon
   */
  setupNotificationIcon() {
    const icon = document.querySelector('.notification-icon');
    if (icon) {
      icon.addEventListener('click', () => console.log('Notification menu'));
    }
  }

  /**
   * Execute command with visual feedback
   */
  executeCommand() {
    const value = this.commandInput.value.trim();
    if (!value) return;

    this.executeButton.textContent = 'Processing...';
    this.executeButton.disabled = true;
    this.executeButton.style.opacity = '0.7';

    setTimeout(() => {
      this.executeButton.innerHTML = '✓ Executed <span class="execute-key" aria-hidden="true">↵</span>';
      this.executeButton.style.background = 'linear-gradient(135deg, #A78BFA 0%, #8B5CF6 100%)';
      this.executeButton.style.color = '#F5F3FF';

      setTimeout(() => {
        this.executeButton.innerHTML = 'Execute <span class="execute-key" aria-hidden="true">↵</span>';
        this.executeButton.disabled = false;
        this.executeButton.style.opacity = '1';
        this.executeButton.style.background = '';
        this.executeButton.style.color = '';
        this.commandInput.value = '';
      }, 1500);
    }, 800);

    console.log(`Command: ${value}`);
  }

  /**
   * Setup metrics counter animation
   */
  setupMetricsAnimation() {
    document.querySelectorAll('.metric-value[data-count-to]').forEach((metric) => {
      const target = parseInt(metric.getAttribute('data-count-to'), 10);
      if (Number.isNaN(target)) return;

      let current = 0;
      const increment = Math.max(1, Math.ceil(target / 36));
      const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        metric.textContent = current.toLocaleString('en-US');
      }, 40);
    });
  }

  /**
   * Auth modal: sidebar triggers, tabs, validation, ESC / backdrop close
   */
  setupAuthModal() {
    const root = document.getElementById('auth-modal-root');
    if (!root) return;

    const dialog = root.querySelector('.auth-modal__dialog');
    const closeBtn = document.getElementById('auth-modal-close');
    const openLogin = document.getElementById('auth-open-login');
    const openSignup = document.getElementById('auth-open-signup');
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

    this._authLastFocus = null;
    this._authCloseTimer = null;

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
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
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
      tabLogin.setAttribute('aria-selected', login ? 'true' : 'false');
      tabSignup.setAttribute('aria-selected', login ? 'false' : 'true');
      tabLogin.tabIndex = login ? 0 : -1;
      tabSignup.tabIndex = login ? -1 : 0;
      panelLogin.classList.toggle('is-active', login);
      panelSignup.classList.toggle('is-active', !login);
      if (subtitle) {
        subtitle.textContent = login ? 'Sign in to continue' : 'Create your account';
      }
      if (login) {
        const el = document.getElementById('auth-login-email');
        if (el) setTimeout(() => el.focus(), 50);
      } else {
        const el = document.getElementById('auth-signup-name');
        if (el) setTimeout(() => el.focus(), 50);
      }
    };

    tabLogin.addEventListener('click', () => setTab('login'));
    tabSignup.addEventListener('click', () => setTab('signup'));
    tabLogin.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') {
        e.preventDefault();
        tabSignup.focus();
        setTab('signup');
      }
    });
    tabSignup.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') {
        e.preventDefault();
        tabLogin.focus();
        setTab('login');
      }
    });

    const getFocusables = () => {
      if (!dialog) return [];
      return Array.from(
        dialog.querySelectorAll(
          'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )
      ).filter((el) => {
        const style = window.getComputedStyle(el);
        return style.display !== 'none' && style.visibility !== 'hidden';
      });
    };

    const openAuth = (mode) => {
      this._authLastFocus = document.activeElement;
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
      clearTimeout(this._authCloseTimer);
      this._authCloseTimer = setTimeout(() => {
        root.setAttribute('hidden', '');
        root.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('auth-modal-open');
        setFlash('');
        if (this._authLastFocus && typeof this._authLastFocus.focus === 'function') {
          this._authLastFocus.focus();
        }
      }, 320);
    };

    openLogin?.addEventListener('click', (e) => {
      e.preventDefault();
      openAuth('login');
    });
    openSignup?.addEventListener('click', (e) => {
      e.preventDefault();
      openAuth('signup');
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
        return;
      }
      if (e.key !== 'Tab' || !dialog) return;
      const list = getFocusables();
      if (!list.length) return;
      const first = list[0];
      const last = list[list.length - 1];
      if (e.shiftKey) {
        if (document.activeElement === first) {
          e.preventDefault();
          last.focus();
        }
      } else if (document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    });

    const loginEmail = document.getElementById('auth-login-email');
    const loginPass = document.getElementById('auth-login-password');

    const validateLoginField = (el, wrapId, msgId) => {
      const wrap = document.getElementById(wrapId);
      const msg = document.getElementById(msgId);
      if (!el || !wrap) return;
      const v = el.value.trim();
      if (el.type === 'email') {
        if (!v) setFieldState(wrap, msg, '', null);
        else if (!isValidEmail(v)) setFieldState(wrap, msg, 'Enter a valid email address.', 'error');
        else setFieldState(wrap, msg, 'Looks good.', 'ok');
      } else {
        if (!v) setFieldState(wrap, msg, '', null);
        else if (v.length < 6) setFieldState(wrap, msg, 'At least 6 characters.', 'error');
        else setFieldState(wrap, msg, 'Ready to sign in.', 'ok');
      }
    };

    loginEmail?.addEventListener('input', () =>
      validateLoginField(loginEmail, 'auth-wrap-login-email', 'auth-login-email-msg')
    );
    loginEmail?.addEventListener('blur', () =>
      validateLoginField(loginEmail, 'auth-wrap-login-email', 'auth-login-email-msg')
    );
    loginPass?.addEventListener('input', () =>
      validateLoginField(loginPass, 'auth-wrap-login-password', 'auth-login-password-msg')
    );

    document.getElementById('auth-forgot')?.addEventListener('click', () => {
      setFlash('If an account exists, a reset link was sent (demo).');
    });

    const fakeSubmit = (btn, msg) => {
      if (!btn || btn.classList.contains('is-loading')) return;
      btn.classList.add('is-loading');
      btn.disabled = true;
      setTimeout(() => {
        btn.classList.remove('is-loading');
        btn.disabled = false;
        setFlash(msg);
      }, 1500);
    };

    formLogin?.addEventListener('submit', (e) => {
      e.preventDefault();
      const emailOk = isValidEmail(loginEmail?.value.trim() || '');
      const passOk = (loginPass?.value.length || 0) >= 6;
      if (!emailOk) {
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
      fakeSubmit(btnLogin, 'Signed in (demo). Session not persisted.');
    });

    const signupName = document.getElementById('auth-signup-name');
    const signupEmail = document.getElementById('auth-signup-email');
    const signupPass = document.getElementById('auth-signup-password');
    const signupConfirm = document.getElementById('auth-signup-confirm');
    const terms = document.getElementById('auth-signup-terms');
    const termsMsg = document.getElementById('auth-terms-msg');

    signupPass?.addEventListener('input', updateStrength);

    const runSignupValidation = (showAll) => {
      let ok = true;
      const vName = signupName?.value.trim() || '';
      const vEmail = signupEmail?.value.trim() || '';
      const vPass = signupPass?.value || '';
      const vConf = signupConfirm?.value || '';

      if (signupName) {
        if (!vName && showAll) {
          setFieldState(
            document.getElementById('auth-wrap-signup-name'),
            document.getElementById('auth-signup-name-msg'),
            'Name is required.',
            'error'
          );
          ok = false;
        } else if (vName && vName.length < 2) {
          setFieldState(
            document.getElementById('auth-wrap-signup-name'),
            document.getElementById('auth-signup-name-msg'),
            'At least 2 characters.',
            'error'
          );
          ok = false;
        } else if (vName) {
          setFieldState(
            document.getElementById('auth-wrap-signup-name'),
            document.getElementById('auth-signup-name-msg'),
            'Looks good.',
            'ok'
          );
        } else {
          setFieldState(document.getElementById('auth-wrap-signup-name'), document.getElementById('auth-signup-name-msg'), '', null);
        }
      }

      if (signupEmail) {
        if (!vEmail && showAll) {
          setFieldState(
            document.getElementById('auth-wrap-signup-email'),
            document.getElementById('auth-signup-email-msg'),
            'Email is required.',
            'error'
          );
          ok = false;
        } else if (vEmail && !isValidEmail(vEmail)) {
          setFieldState(
            document.getElementById('auth-wrap-signup-email'),
            document.getElementById('auth-signup-email-msg'),
            'Invalid email format.',
            'error'
          );
          ok = false;
        } else if (vEmail) {
          setFieldState(
            document.getElementById('auth-wrap-signup-email'),
            document.getElementById('auth-signup-email-msg'),
            'Valid email.',
            'ok'
          );
        } else {
          setFieldState(document.getElementById('auth-wrap-signup-email'), document.getElementById('auth-signup-email-msg'), '', null);
        }
      }

      if (signupPass) {
        if (!vPass && showAll) {
          setFieldState(
            document.getElementById('auth-wrap-signup-password'),
            document.getElementById('auth-signup-password-msg'),
            'Password is required.',
            'error'
          );
          ok = false;
        } else if (vPass && vPass.length < 8) {
          setFieldState(
            document.getElementById('auth-wrap-signup-password'),
            document.getElementById('auth-signup-password-msg'),
            'Minimum 8 characters.',
            'error'
          );
          ok = false;
        } else if (vPass && scorePassword(vPass) < 3) {
          setFieldState(
            document.getElementById('auth-wrap-signup-password'),
            document.getElementById('auth-signup-password-msg'),
            'Add mixed case, numbers, or symbols.',
            'error'
          );
          ok = false;
        } else if (vPass) {
          setFieldState(
            document.getElementById('auth-wrap-signup-password'),
            document.getElementById('auth-signup-password-msg'),
            'Password meets guidelines.',
            'ok'
          );
        } else {
          setFieldState(
            document.getElementById('auth-wrap-signup-password'),
            document.getElementById('auth-signup-password-msg'),
            '',
            null
          );
        }
      }

      if (signupConfirm) {
        if (!vConf && showAll) {
          setFieldState(
            document.getElementById('auth-wrap-signup-confirm'),
            document.getElementById('auth-signup-confirm-msg'),
            'Confirm your password.',
            'error'
          );
          ok = false;
        } else if (vConf && vConf !== vPass) {
          setFieldState(
            document.getElementById('auth-wrap-signup-confirm'),
            document.getElementById('auth-signup-confirm-msg'),
            'Passwords do not match.',
            'error'
          );
          ok = false;
        } else if (vConf && vPass) {
          setFieldState(
            document.getElementById('auth-wrap-signup-confirm'),
            document.getElementById('auth-signup-confirm-msg'),
            'Passwords match.',
            'ok'
          );
        } else {
          setFieldState(
            document.getElementById('auth-wrap-signup-confirm'),
            document.getElementById('auth-signup-confirm-msg'),
            '',
            null
          );
        }
      }

      if (terms && termsMsg) {
        if (!terms.checked && showAll) {
          termsMsg.textContent = 'You must accept the terms to continue.';
          termsMsg.classList.add('auth-field-msg--error');
          ok = false;
        } else {
          termsMsg.textContent = '';
          termsMsg.classList.remove('auth-field-msg--error');
        }
      }

      return ok;
    };

    ['input', 'blur'].forEach((evt) => {
      signupName?.addEventListener(evt, () => runSignupValidation(false));
      signupEmail?.addEventListener(evt, () => runSignupValidation(false));
      signupPass?.addEventListener(evt, () => runSignupValidation(false));
      signupConfirm?.addEventListener(evt, () => runSignupValidation(false));
      terms?.addEventListener('change', () => runSignupValidation(false));
    });

    formSignup?.addEventListener('submit', (e) => {
      e.preventDefault();
      if (!runSignupValidation(true)) return;
      fakeSubmit(btnSignup, 'Account created (demo). You can close this window.');
    });
  }
}

/**
 * Initialize on DOM ready
 */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    new ElevUraDashboard();
  });
} else {
  new ElevUraDashboard();
}
