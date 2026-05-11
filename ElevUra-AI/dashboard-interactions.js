/**
 * ElevUra Dashboard - Dashboard Interactions
 * Handles user interactions and command execution
 */

class ElevUraDashboard {
  constructor() {
    this.init();
    this.setupInteractions();
    this.setupMetricsAnimation();
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
    document.querySelectorAll('.sidebar-item').forEach(item => {
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
