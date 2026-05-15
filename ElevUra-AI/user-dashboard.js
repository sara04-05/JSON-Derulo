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

  const EMPTY_ICONS = {
    cvs: '<svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>',
    jobs: '<svg viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>',
    courses: '<svg viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 0 6-2 6-3v-5"/></svg>',
    interviews: '<svg viewBox="0 0 24 24"><path d="M12 2a3 3 0 0 0-3 3v4a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v1a7 7 0 0 1-14 0v-1"/></svg>',
    default: '<svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v4M12 16h.01"/></svg>',
  };

  function emptyCard(message, type = 'default') {
    const icon = EMPTY_ICONS[type] || EMPTY_ICONS.default;
    return `<article class="ud-card ud-empty-card">
      <div class="ud-empty-icon" aria-hidden="true">${icon}</div>
      <p class="ud-empty-title">Nothing here yet</p>
      <p class="ud-card-meta">${message}</p>
    </article>`;
  }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str ?? '';
    return d.innerHTML;
  }

  function renderCVs(container, items) {
    if (!container) return;
    if (!items?.length) {
      container.innerHTML = emptyCard('Use CV Optimizer on the Command Center to upload your first document.', 'cvs');
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
      container.innerHTML = emptyCard('Track applications from the Command Center to see your pipeline here.', 'jobs');
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
      container.innerHTML = emptyCard('Start learning with Study Buddy to build your course progress.', 'courses');
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

  function renderInterviews(container, items) {
    if (!container) return;
    if (!items?.length) {
      container.innerHTML = emptyCard('Complete a mock interview on the Command Center to see sessions here.', 'interviews');
      return;
    }
    container.innerHTML = items
      .map(
        (i) => `
      <article class="ud-card ud-interview-card">
        <div class="ud-interview-card__head">
          <h4 class="ud-card-title">Session · ${escapeHtml(i.date)}</h4>
          <span class="ud-interview-score">${i.interview_score}</span>
        </div>
        <div class="ud-interview-metrics">
          <span class="ud-card-meta">Communication ${i.communication_score}%</span>
          <span class="ud-card-meta">Confidence ${i.confidence_score}%</span>
        </div>
        <p class="ud-card-meta ud-interview-feedback">${escapeHtml((i.ai_feedback || '').slice(0, 120))}${(i.ai_feedback || '').length > 120 ? '…' : ''}</p>
      </article>`
      )
      .join('');
  }

  function updateQuickStats(data) {
    const cvs = document.getElementById('ud-stat-cvs');
    const jobs = document.getElementById('ud-stat-jobs');
    const courses = document.getElementById('ud-stat-courses');
    const score = document.getElementById('ud-stat-score');
    if (cvs) cvs.textContent = String(data.cvs?.length ?? 0);
    if (jobs) jobs.textContent = String(data.applied_jobs?.length ?? 0);
    if (courses) courses.textContent = String(data.courses?.length ?? 0);
    if (score) score.textContent = String(data.analytics?.overall_score ?? 0);
  }

  function renderAnalytics(analytics, interviews) {
    const overallEl = document.getElementById('ud-overall-score');
    const commBar = document.getElementById('ud-comm-bar');
    const confBar = document.getElementById('ud-conf-bar');
    const commScore = document.getElementById('ud-comm-score');
    const confScore = document.getElementById('ud-conf-score');
    const feedback = document.getElementById('ud-feedback-text');
    const sparkline = document.getElementById('ud-sparkline');

    const overall = analytics?.overall_score ?? 0;
    const comm = analytics?.communication_score ?? 0;
    const conf = analytics?.confidence_score ?? 0;

    if (overallEl) overallEl.textContent = String(overall);
    if (commBar) commBar.style.setProperty('--progress', `${comm}%`);
    if (confBar) confBar.style.setProperty('--progress', `${conf}%`);
    if (commScore) commScore.textContent = `${comm}%`;
    if (confScore) confScore.textContent = `${conf}%`;
    if (feedback) {
      feedback.textContent =
        analytics?.ai_feedback || 'Complete a mock interview on the Command Center to receive AI feedback.';
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
    updateQuickStats(data);
    renderCVs(document.getElementById('ud-cvs-grid'), data.cvs);
    renderJobs(document.getElementById('ud-jobs-grid'), data.applied_jobs);
    renderCourses(document.getElementById('ud-courses-grid'), data.courses);
    renderAnalytics(data.analytics, data.mock_interviews);
    renderInterviews(document.getElementById('ud-interviews-grid'), data.mock_interviews);
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
