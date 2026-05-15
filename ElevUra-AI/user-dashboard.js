/**
 * ElevUra — user dashboard page (separate from homepage)
 */
(function () {
  const STATUS_CLASS = {
    Applied: 'job-status--cyan',
    Interviewing: 'job-status--purple',
    Accepted: 'job-status--green',
    Rejected: 'job-status--red',
  };

  const SECTION_MAP = {
    cvs: 'my-cvs',
    jobs: 'applied-jobs',
    courses: 'courses-completed',
    interviews: 'mock-interviews',
    settings: 'account-settings',
    overview: null,
  };

  function isDashboardPage() {
    return document.body.dataset.page === 'user-dashboard';
  }

  function scoreClass(score) {
    if (score >= 90) return 'ats-score--excellent';
    if (score >= 80) return 'ats-score--good';
    return 'ats-score--fair';
  }

  function emptyCard(message) {
    return `<article class="ud-card ud-empty-card"><p class="ud-card-meta">${message}</p></article>`;
  }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
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
    const section = document.getElementById('mock-interviews');
    if (!section || !analytics) return;

    const overall = section.querySelector('.ud-stat-value');
    const commBar = section.querySelector('.ud-stat-card:nth-child(2) .ud-progress__bar');
    const confBar = section.querySelector('.ud-stat-card:nth-child(3) .ud-progress__bar');
    const feedback = section.querySelector('.ud-feedback-text');
    const sparkline = section.querySelector('.ud-sparkline');

    if (overall) {
      overall.innerHTML = `${analytics.overall_score || 0} <span class="ud-stat-delta ud-stat-delta--up">latest</span>`;
    }
    if (commBar) commBar.style.setProperty('--progress', `${analytics.communication_score || 0}%`);
    if (confBar) confBar.style.setProperty('--progress', `${analytics.confidence_score || 0}%`);
    if (feedback) {
      feedback.textContent = analytics.ai_feedback || 'Complete a mock interview to receive AI feedback.';
    }

    if (sparkline && interviews?.length) {
      const trend = interviews.slice(0, 7).reverse();
      const max = Math.max(...trend.map((i) => i.interview_score), 1);
      sparkline.innerHTML = trend
        .map((i) => `<span style="--h:${Math.round((i.interview_score / max) * 100)}%"></span>`)
        .join('');
    }
  }

  function refreshFromAuth() {
    const data = window.ElevUraAuth?.getDashboardData();
    if (!data) return;
    renderCVs(document.getElementById('ud-cvs-grid'), data.cvs);
    renderJobs(document.getElementById('ud-jobs-grid'), data.applied_jobs);
    renderCourses(document.getElementById('ud-courses-grid'), data.courses);
    renderAnalytics(data.analytics, data.mock_interviews);
  }

  function scrollToSection(navKey) {
    const id = SECTION_MAP[navKey] || null;
    if (!id) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return;
    }
    const el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    setActiveNav(navKey);
  }

  function setActiveNav(navKey) {
    document.querySelectorAll('.ud-nav-btn').forEach((btn) => {
      const key = btn.getAttribute('data-ud-nav');
      btn.classList.toggle('is-active', key === navKey);
    });
  }

  function resolveHash() {
    const hash = (location.hash || '').replace('#', '');
    if (!hash) {
      setActiveNav('cvs');
      return;
    }
    const entry = Object.entries(SECTION_MAP).find(([, id]) => id === hash);
    if (entry) {
      scrollToSection(entry[0]);
    } else {
      const el = document.getElementById(hash);
      if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function initNav() {
    document.querySelectorAll('.ud-nav-btn').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        const key = btn.getAttribute('data-ud-nav');
        if (btn.getAttribute('href')?.startsWith('#')) {
          e.preventDefault();
          history.replaceState(null, '', btn.getAttribute('href'));
        }
        scrollToSection(key || 'overview');
      });
    });

    window.addEventListener('hashchange', resolveHash);
  }

  function init() {
    if (!isDashboardPage()) return;

    if (!window.ElevUraAuth?.isLoggedIn()) {
      window.ElevUraAuthUI?.openAuth('login');
    }

    initNav();
    resolveHash();
    refreshFromAuth();
    window.addEventListener('elevura:auth-change', () => {
      refreshFromAuth();
      if (!window.ElevUraAuth?.isLoggedIn()) {
        window.ElevUraAuthUI?.openAuth('login');
      }
    });
  }

  window.ElevUraDashboardData = { refresh: refreshFromAuth };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
