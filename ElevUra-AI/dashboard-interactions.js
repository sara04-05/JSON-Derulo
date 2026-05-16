/**
 * ElevUra Dashboard - Dashboard Interactions
 */

class ElevUraDashboard {
  constructor() {
    this.init();
    this.setupInteractions();
    this.setupMetricsAnimation();
    this.scrollToHashTarget();
  }

  init() {
    this.commandInput = document.querySelector('.command-input');
    this.executeButton = document.querySelector('.execute-button');
  }

  setupInteractions() {
    this.setupSidebarNavigation();

    if (this.commandInput) {
      this.commandInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') this.executeCommand();
      });
    }

    if (this.executeButton) {
      this.executeButton.addEventListener('click', () => this.executeCommand());
    }

    this.setupNotificationIcon();
    this.setupFullscreenToggle();
  }

  setupSidebarNavigation() {
    document.querySelectorAll('.sidebar-menu .sidebar-item[data-view="command"]').forEach((item) => {
      item.addEventListener('click', (e) => {
        if (window.ElevUraAuth && !window.ElevUraAuth.isLoggedIn()) return;
        const onHome =
          document.body.dataset.page === 'home' &&
          !window.location.pathname.replace(/\\/g, '/').includes('user_dashboard');
        if (onHome) {
          e.preventDefault();
          document.querySelectorAll('.sidebar-item').forEach((i) => i.classList.remove('active'));
          item.classList.add('active');
          window.scrollTo({ top: 0, behavior: 'smooth' });
          if (window.location.hash) {
            history.replaceState(null, '', 'index.php');
          }
        }
      });
    });
  }

  scrollToHashTarget() {
    const hash = window.location.hash;
    if (!hash) return;
    const target = document.querySelector(hash);
    if (target) {
      requestAnimationFrame(() => {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }
  }

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

  setupNotificationIcon() {
    const icon = document.querySelector('.notification-icon');
    if (icon) icon.addEventListener('click', () => console.log('Notification menu'));
  }

  executeCommand() {
    if (window.ElevUraProtected && !window.ElevUraAuth?.isLoggedIn()) {
      window.ElevUraAuthUI?.openAuth('login');
      return;
    }

    const value = this.commandInput?.value.trim();
    if (!value) return;

    this.executeButton.textContent = 'Processing...';
    this.executeButton.disabled = true;
    this.executeButton.style.opacity = '0.7';

    setTimeout(() => {
      this.executeButton.innerHTML =
        'Executed <span class="execute-key" aria-hidden="true">Enter</span>';
      this.executeButton.style.background = 'linear-gradient(135deg, #A78BFA 0%, #8B5CF6 100%)';
      this.executeButton.style.color = '#F5F3FF';

      setTimeout(() => {
        this.executeButton.innerHTML =
          'Execute <span class="execute-key" aria-hidden="true">Enter</span>';
        this.executeButton.disabled = false;
        this.executeButton.style.opacity = '1';
        this.executeButton.style.background = '';
        this.executeButton.style.color = '';
        if (this.commandInput) this.commandInput.value = '';
      }, 1500);
    }, 800);
  }

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
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => new ElevUraDashboard());
} else {
  new ElevUraDashboard();
}
