/**
 * ElevUra — user dashboard view + mock data
 */
(function () {
  const MOCK_CVS = [
    { title: 'Software Engineer — ATS Optimized', score: 92, edited: 'May 12, 2026' },
    { title: 'Product Designer Portfolio CV', score: 87, edited: 'May 3, 2026' },
    { title: 'Graduate Data Analyst', score: 78, edited: 'Apr 28, 2026' },
  ];

  const MOCK_JOBS = [
    { company: 'NovaTech', role: 'Junior Developer', date: 'May 10, 2026', status: 'Interviewing' },
    { company: 'Helix Health', role: 'Data Analyst Intern', date: 'May 2, 2026', status: 'Applied' },
    { company: 'Orbit Labs', role: 'UX Research Associate', date: 'Apr 18, 2026', status: 'Rejected' },
    { company: 'Pulse AI', role: 'ML Engineer Trainee', date: 'Apr 5, 2026', status: 'Accepted' },
  ];

  const MOCK_COURSES = [
    { name: 'Advanced SQL for Analysts', progress: 100, status: 'Completed', badge: 'Mastered' },
    { name: 'System Design Fundamentals', progress: 72, status: 'In Progress', badge: 'On Track' },
    { name: 'Behavioral Interview Mastery', progress: 45, status: 'In Progress', badge: 'Building' },
  ];

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

  function renderCVs(container) {
    if (!container) return;
    container.innerHTML = MOCK_CVS.map(
      (cv) => `
      <article class="ud-card ud-cv-card">
        <div class="ud-cv-card__top">
          <h4 class="ud-card-title">${cv.title}</h4>
          <div class="ats-score ${scoreClass(cv.score)}" data-score="${cv.score}">
            <span class="ats-score__value">${cv.score}</span>
            <span class="ats-score__label">ATS</span>
          </div>
        </div>
        <p class="ud-card-meta">Last edited · ${cv.edited}</p>
        <div class="ud-card-actions">
          <button type="button" class="ud-btn ud-btn--ghost">Edit</button>
          <button type="button" class="ud-btn ud-btn--primary">Download</button>
        </div>
      </article>`
    ).join('');
  }

  function renderJobs(container) {
    if (!container) return;
    container.innerHTML = MOCK_JOBS.map(
      (job) => `
      <article class="ud-card ud-job-card">
        <div class="ud-job-card__head">
          <div>
            <h4 class="ud-card-title">${job.role}</h4>
            <p class="ud-card-meta">${job.company}</p>
          </div>
          <span class="job-status ${STATUS_CLASS[job.status] || ''}">${job.status}</span>
        </div>
        <p class="ud-card-meta">Applied · ${job.date}</p>
      </article>`
    ).join('');
  }

  function renderCourses(container) {
    if (!container) return;
    container.innerHTML = MOCK_COURSES.map(
      (c) => `
      <article class="ud-card ud-course-card">
        <div class="ud-course-card__head">
          <h4 class="ud-card-title">${c.name}</h4>
          <span class="ud-achievement-badge">${c.badge}</span>
        </div>
        <div class="ud-progress">
          <div class="ud-progress__bar" style="--progress: ${c.progress}%"></div>
        </div>
        <div class="ud-course-card__foot">
          <span class="ud-card-meta">${c.status}</span>
          <span class="ud-progress__pct">${c.progress}%</span>
        </div>
      </article>`
    ).join('');
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
      btn.classList.toggle('is-active', btn.getAttribute('data-ud-nav') === section);
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
    renderCVs(document.getElementById('ud-cvs-grid'));
    renderJobs(document.getElementById('ud-jobs-grid'));
    renderCourses(document.getElementById('ud-courses-grid'));
    initNav();
  }

  window.ElevUraViews = { showCommandCenter, showUserDashboard };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
