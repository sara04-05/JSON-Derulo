/**
 * ElevUra Dashboard - Advanced Micro-Interactions Script
 * Adds premium animations, visual feedback, and intelligent interactions
 */

class ElevUraDashboard {
  constructor() {
    this.init();
    this.setupInteractions();
    this.setupMetricsAnimation();
    this.setupScrollEffects();
  }

  init() {
    this.sidebar = document.querySelector('.sidebar');
    this.mainContent = document.querySelector('.main-content');
    this.moduleCards = document.querySelectorAll('.module-card');
    this.commandInput = document.querySelector('.command-input');
    this.executeButton = document.querySelector('.execute-button');
    this.contentArea = document.querySelector('.content-area');
  }

  /**
   * Setup interactive behaviors for sidebar and navigation
   */
  setupInteractions() {
    // Sidebar item active state with smooth transition
    document.querySelectorAll('.sidebar-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Remove active class from all
        document.querySelectorAll('.sidebar-item').forEach(i => {
          i.classList.remove('active');
          i.style.transition = 'all 0.3s cubic-bezier(0.22, 1, 0.36, 1)';
        });
        
        // Add to clicked item with delay for smooth effect
        item.classList.add('active');
        
        // Ripple effect (optional)
        this.createRipple(item, e);
      });

      // Hover state tracking
      item.addEventListener('mouseenter', function() {
        this.style.transform = 'translateX(4px)';
      });
      
      item.addEventListener('mouseleave', function() {
        if (!this.classList.contains('active')) {
          this.style.transform = 'translateX(0)';
        }
      });
    });

    // Module card enhanced interactions
    this.moduleCards.forEach(card => {
      card.addEventListener('mouseenter', (e) => {
        this.createCardGlow(card, e);
      });

      card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)';
        card.style.boxShadow = card.getAttribute('data-default-shadow') || 
          '0 0 40px rgba(0, 229, 255, 0.08)';
      });

      card.addEventListener('click', () => {
        this.cardClickFeedback(card);
      });
    });

    // Command input enhanced focus
    this.commandInput.addEventListener('focus', () => {
      this.commandInputFocus(true);
    });

    this.commandInput.addEventListener('blur', () => {
      this.commandInputFocus(false);
    });

    // Execute button with loading state
    this.executeButton.addEventListener('click', () => {
      this.executeCommand();
    });

    // Allow Enter key to execute
    this.commandInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        this.executeCommand();
      }
    });

    // Notification icon pulse
    this.setupNotificationPulse();
    this.setupFullscreenToggle();
  }

  setupFullscreenToggle() {
    const btn = document.querySelector('.header-fullscreen');
    if (!btn) return;
    btn.addEventListener('click', () => {
      const doc = document.documentElement;
      if (!document.fullscreenElement) {
        doc.requestFullscreen?.().catch(() => {});
      } else {
        document.exitFullscreen?.().catch(() => {});
      }
    });
  }

 
  createRipple(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;

    ripple.style.cssText = `
      position: absolute;
      width: ${size}px;
      height: ${size}px;
      border-radius: 50%;
      transform: scale(0);
      animation: rippleAnimation 0.6s ease-out;
      pointer-events: none;
      left: ${x}px;
      top: ${y}px;
    `;

    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);

    setTimeout(() => ripple.remove(), 600);
  }

  /**
   * Create card glow and lift effect
   */
  createCardGlow(card, event) {
    const rect = card.getBoundingClientRect();
    const x = event.clientX - rect.left;
    const y = event.clientY - rect.top;

    card.style.transform = 'translateY(-4px) scale(1.01)';
    card.style.boxShadow = `
     
    `;
  }

  /**
   * Card click feedback
   */
  cardClickFeedback(card) {
    const originalTransform = card.style.transform;
    card.style.transform = 'translateY(-4px) scale(0.98)';
    
    setTimeout(() => {
      card.style.transform = originalTransform;
    }, 100);

    // Log module interaction
    const title = card.querySelector('.module-title')?.textContent;
    console.log(`Module clicked: ${title}`);
  }

  /**
   * Command input focus effect
   */
  commandInputFocus(isFocused) {
    const wrapper = this.commandInput.parentElement;
    
    
  }

  /**
   * Execute command with visual feedback
   */
  executeCommand() {
    const value = this.commandInput.value.trim();
    
    if (!value) return;

    // Visual feedback
    this.executeButton.textContent = 'Processing...';
    this.executeButton.disabled = true;
    this.executeButton.style.opacity = '0.7';

    // Simulate command execution
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

    console.log(`Command executed: ${value}`);
  }

  /**
   * Setup metrics counter animation
   */
  setupMetricsAnimation() {
    document.querySelectorAll('.metric-value[data-count-to]').forEach((metric) => {
      const target = parseInt(metric.getAttribute('data-count-to'), 10);
      if (Number.isNaN(target)) return;

      let current = 0;
      const steps = 36;
      const increment = Math.max(1, Math.ceil(target / steps));
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
   * Format metric values with proper suffixes
   */
  formatMetricValue(value, suffix = '') {
    if (value >= 1000000) {
      return (value / 1000000).toFixed(1) + 'M' + suffix;
    } else if (value >= 1000) {
      return (value / 1000).toFixed(1) + 'K' + suffix;
    }
    return value + suffix;
  }

  /**
   * Setup scroll effects and parallax
   */
  setupScrollEffects() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
          observer.unobserve(entry.target);
        }
      });
    }, observerOptions);

    // Observe module cards for fade-in animation
    document.querySelectorAll('.module-card').forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = `all 0.5s cubic-bezier(0.22, 1, 0.36, 1) ${index * 100}ms`;
      observer.observe(card);
    });

    // Observe analytics panels
    document.querySelectorAll('.analytics-panel').forEach((panel, index) => {
      panel.style.opacity = '0';
      panel.style.transform = 'translateY(20px)';
      panel.style.transition = `all 0.5s cubic-bezier(0.22, 1, 0.36, 1) ${(index + 4) * 100}ms`;
      observer.observe(panel);
    });
  }

  /**
   * Setup notification icon pulse
   */
  setupNotificationPulse() {
    const notificationIcon = document.querySelector('.notification-icon');
    
    if (notificationIcon) {
      notificationIcon.addEventListener('click', () => {
        this.showNotificationMenu();
      });

      // Auto-pulse effect
      setInterval(() => {
        notificationIcon.style.animation = 'none';
        setTimeout(() => {
          notificationIcon.style.animation = 'notificationPulse 0.5s ease-out';
        }, 10);
      }, 5000);
    }
  }

  /**
   * Show notification menu
   */
  showNotificationMenu() {
    console.log('Notification menu opened');
    // Implementation for notification dropdown
  }
}

/**
 * Add CSS animations dynamically
 */
function injectDynamicStyles() {
  const style = document.createElement('style');
  style.textContent = `
    @keyframes rippleAnimation {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }

    @keyframes notificationPulse {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.1);
      }
    }

    @keyframes slideInCard {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes glowPulse {
      0%, 100% {
        box-shadow: 0 0 20px rgba(0, 229, 255, 0.1);
      }
      50% {
        box-shadow: 0 0 40px rgba(0, 229, 255, 0.2);
      }
    }

    /* Smooth transitions for all interactive elements */
    button, input, a, [role="button"] {
      transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1) !important;
    }
  `;
  
  document.head.appendChild(style);
}

/**
 * Initialize on DOM ready
 */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    injectDynamicStyles();
    new ElevUraDashboard();
  });
} else {
  injectDynamicStyles();
  new ElevUraDashboard();
}

/**
 * Export for use in other modules
 */
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ElevUraDashboard;
}
