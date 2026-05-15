/**
 * ElevUra — user dashboard view (data from PHP / MySQL)
 */
(function () {
  const STATUS_CLASS = {
    Applied: 'job-status--cyan',
    Interviewing: 'job-status--purple',
    Accepted: 'job-status--green',
    Rejected: 'job-status--red',
  };

  function scoreClass(score) {
    if (score >= 90) return 'ats-score--excellent';
    if (score >= 80) return 'ats-score--good';
    return 'ats-score--fair';
  }

  function emptyCard(message) {
    return `<article class="ud-card ud-empty-card"><p class="ud-card-meta">${message}</p></article>`;
  }

  function renderCVs(container, items) {
    if (!container) return;
    if (!items?.length) {
      container.innerHTML = emptyCard('No CVs yet. Use CV Optimizer to upload your first document.');
      return;
    }
    container.innerHTML = items
      .map(
        (cv) => `
      <article class="ud-card ud-cv-card">
        <div class="ud-cv-card__top">
          <h4 class="ud-card-title">${escapeHtml(cv.title)}</h4>
          <div class="ats-score ${scoreClass(cv.score)}" data-score="${cv.score}">
            <span class="ats-score__value">${cv.score}</span>
            <span class="ats-score__label">ATS</span>
          </div>
        </div>
        <p class="ud-card-meta">Last edited · ${escapeHtml(cv.edited)}</p>
        <div class="ud-card-actions">
          <button type="button" class="ud-btn ud-btn--ghost">Edit</button>
          <button type="button" class="ud-btn ud-btn--primary">Download</button>
        </div>
      </article>`
      )
      .join('');
  }

  function renderJobs(container, items) {
    if (!container) return;
    if (!items?.length) {
      container.innerHTML = emptyCard('No applications tracked yet.');
      return;
    }
    container.innerHTML = items
      .map(
        (job) => `
      <article class="ud-card ud-job-card">
        <div class="ud-job-card__head">
          <div>
            <h4 class="ud-card-title">${escapeHtml(job.role)}</h4>
            <p class="ud-card-meta">${escapeHtml(job.company)}</p>
          </div>
          <span class="job-status ${STATUS_CLASS[job.status] || ''}">${escapeHtml(job.status)}</span>
        </div>
        <p class="ud-card-meta">Applied · ${escapeHtml(job.date)}</p>
      </article>`
      )
      .join('');
  }

  function renderCourses(container, items) {
    if (!container) return;
    if (!items?.length) {
      container.innerHTML = emptyCard('No courses in progress. Start learning with Study Buddy.');
      return;
    }
    container.innerHTML = items
      .map(
        (c) => `
      <article class="ud-card ud-course-card">
        <div class="ud-course-card__head">
          <h4 class="ud-card-title">${escapeHtml(c.name)}</h4>
          <span class="ud-achievement-badge">${escapeHtml(c.badge)}</span>
        </div>
        <div class="ud-progress">
          <div class="ud-progress__bar" style="--progress: ${c.progress}%"></div>
        </div>
        <div class="ud-course-card__foot">
          <span class="ud-card-meta">${escapeHtml(c.status)}</span>
          <span class="ud-progress__pct">${c.progress}%</span>
        </div>
      </article>`
      )
      .join('');
  }

  function renderAnalytics(analytics, interviews) {
    const overall = document.querySelector('#ud-section-interviews .ud-stat-value');
    const commBar = document.querySelector('#ud-section-interviews .ud-stat-card:nth-child(2) .ud-progress__bar');
    const confBar = document.querySelector('#ud-section-interviews .ud-stat-card:nth-child(3) .ud-progress__bar');
    const feedback = document.querySelector('#ud-section-interviews .ud-feedback-text');
    const sparkline = document.querySelector('#ud-section-interviews .ud-sparkline');

    if (!analytics) return;

    if (overall) {
      overall.innerHTML = `${analytics.overall_score || 0} <span class="ud-stat-delta ud-stat-delta--up">latest</span>`;
    }
    if (commBar) commBar.style.setProperty('--progress', `${analytics.communication_score || 0}%`);
    if (confBar) confBar.style.setProperty('--progress', `${analytics.confidence_score || 0}%`);
    if (feedback) feedback.textContent = analytics.ai_feedback || 'Complete a mock interview to receive AI feedback.';

    if (sparkline && interviews?.length) {
      const trend = interviews.slice(0, 7).reverse();
      const max = Math.max(...trend.map((i) => i.interview_score), 1);
      sparkline.innerHTML = trend
        .map((i) => `<span style="--h:${Math.round((i.interview_score / max) * 100)}%"></span>`)
        .join('');
    }
  }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  function refreshFromAuth() {
    const data = window.ElevUraAuth?.getDashboardData();
    if (!data) return;
    renderCVs(document.getElementById('ud-cvs-grid'), data.cvs);
    renderJobs(document.getElementById('ud-jobs-grid'), data.applied_jobs);
    renderCourses(document.getElementById('ud-courses-grid'), data.courses);
    renderAnalytics(data.analytics, data.mock_interviews);
  }

  function showCommandCenter() {
    const cc = document.getElementById('view-command-center');
    const ud = document.getElementById('view-user-dashboard');
    if (cc) cc.hidden = false;
    if (ud) ud.hidden = true;
    document.querySelector('.sidebar-item[data-view="command"]')?.classList.add('active');
    document.getElementById('sidebar-my-dashboard')?.classList.remove('active');
  }

  function showUserDashboard(section) {
    if (!window.ElevUraAuth?.isLoggedIn()) {
      window.ElevUraAuthUI?.openAuth('login');
      return;
    }
    refreshFromAuth();
    const cc = document.getElementById('view-command-center');
    const ud = document.getElementById('view-user-dashboard');
    if (cc) cc.hidden = true;
    if (ud) {
      ud.hidden = false;
      ud.scrollTop = 0;
    }
    document.querySelector('.sidebar-item[data-view="command"]')?.classList.remove('active');
    document.getElementById('sidebar-my-dashboard')?.classList.add('active');

    const target = section && section !== 'overview' ? document.getElementById(`ud-section-${section}`) : null;
    if (target) {
      setTimeout(() => target.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
    }

    document.querySelectorAll('.ud-nav-btn').forEach((btn) => {
      btn.classList.toggle('is-active', btn.getAttribute('data-ud-nav') === (section || 'overview'));
    });
  }

  function initNav() {
    document.querySelector('.sidebar-item[data-view="command"]')?.addEventListener('click', (e) => {
      e.preventDefault();
      document.querySelectorAll('.sidebar-item').forEach((i) => i.classList.remove('active'));
      e.currentTarget.classList.add('active');
      showCommandCenter();
    });

    document.getElementById('sidebar-my-dashboard')?.addEventListener('click', (e) => {
      e.preventDefault();
      if (!window.ElevUraAuth?.isLoggedIn()) {
        window.ElevUraAuthUI?.openAuth('login');
        return;
      }
      showUserDashboard('overview');
    });

    document.querySelectorAll('.ud-nav-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        showUserDashboard(btn.getAttribute('data-ud-nav') || 'overview');
      });
    });
  }

  function init() {
    initNav();
    window.addEventListener('elevura:auth-change', refreshFromAuth);
    if (window.ElevUraAuth?.isLoggedIn()) refreshFromAuth();
  }

  window.ElevUraViews = { showCommandCenter, showUserDashboard };
  window.ElevUraDashboardData = { refresh: refreshFromAuth };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
