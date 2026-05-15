/**
 * ElevUra — Study Buddy Frontend Logic
 */
(function () {
  function $(sel) { return document.querySelector(sel); }
  function $$(sel) { return document.querySelectorAll(sel); }

  const API_ENDPOINT = 'backend/study_buddy.php';

  function init() {
    const workspace = $('#study-buddy-workspace');
    if (!workspace) return;

    // Handle Study Buddy Card clicks
    $$('.study-buddy-card, .study-buddy-btn').forEach(el => {
      el.addEventListener('click', (e) => {
        if (!window.ElevUraAuth?.isLoggedIn()) return; // Handled by protected-ui.js
        
        const type = el.getAttribute('data-sb-type');
        if (type) {
          openWorkspace(type);
        }
      });
    });

    $('#btnGenerateStudy')?.addEventListener('click', generateMaterials);
    $('#btnResetSB')?.addEventListener('click', resetWorkspace);
  }

  function openWorkspace(type) {
    const workspace = $('#study-buddy-workspace');
    const typeInput = $('#sb-type');
    const titleEl = $('#sb-workspace-title');
    const badgeEl = $('#sb-workspace-badge');

    typeInput.value = type;
    workspace.classList.remove('hidden');
    workspace.scrollIntoView({ behavior: 'smooth' });

    const labels = {
      quiz: { title: 'Generate <span>Adaptive Quiz</span>', badge: 'QUIZ GENERATOR' },
      flashcard: { title: 'Generate <span>Flashcards</span>', badge: 'FLASHCARD CREATOR' },
      explanation: { title: 'AI <span>Explanations</span>', badge: 'TOPIC EXPLORER' }
    };

    const config = labels[type] || labels.quiz;
    titleEl.innerHTML = config.title;
    badgeEl.textContent = config.badge;

    resetWorkspace(false);
  }

  async function generateMaterials() {
    const btn = $('#btnGenerateStudy');
    const errEl = $('#sb-error');
    const resultsArea = $('#sb-results');
    const setupForm = $('#sb-setup-form');
    const container = $('#sb-content-container');

    const data = {
      type: $('#sb-type').value,
      jobTitle: $('#sb-jobTitle').value.trim(),
      jobLevel: $('#sb-seniority').value,
      industry: $('#sb-industry').value.trim(),
      skills: $('#sb-skills').value.trim(),
      jobContext: $('#sb-context').value.trim(),
      geminiKey: $('#sb-geminiKey').value.trim()
    };

    if (!data.jobTitle) {
      showError('Please enter a job title.');
      return;
    }

    setLoading(true);
    showError('');

    try {
      const res = await fetch(API_ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await res.json();
      if (!result.success) throw new Error(result.message || 'Generation failed');

      renderResults(result.data);
      setupForm.classList.add('hidden');
      resultsArea.classList.remove('hidden');
    } catch (err) {
      showError(err.message);
    } finally {
      setLoading(false);
    }
  }

  function renderResults(data) {
    const container = $('#sb-content-container');
    container.innerHTML = '';

    if (data.type === 'quiz') {
      renderQuiz(container, data.items);
    } else if (data.type === 'flashcard') {
      renderFlashcards(container, data.items);
    } else {
      container.innerHTML = `<div class="ud-card"><p>${JSON.stringify(data)}</p></div>`;
    }
  }

  function renderQuiz(container, questions) {
    container.innerHTML = questions.map((q, i) => `
      <article class="ud-card" style="margin-bottom: 20px;">
        <h4 class="ud-card-title">${i + 1}. ${escapeHtml(q.question)}</h4>
        <div class="quiz-options" style="display: grid; gap: 10px; margin-top: 15px;">
          ${q.options.map((opt, oi) => `
            <button type="button" class="ud-btn ud-btn--ghost sb-quiz-opt" data-correct="${oi === q.correct_index}">
              ${escapeHtml(opt)}
            </button>
          `).join('')}
        </div>
      </article>
    `).join('');

    container.querySelectorAll('.sb-quiz-opt').forEach(btn => {
      btn.addEventListener('click', () => {
        const isCorrect = btn.getAttribute('data-correct') === 'true';
        btn.style.background = isCorrect ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)';
        btn.style.borderColor = isCorrect ? '#22c55e' : '#ef4444';
        btn.style.color = '#fff';
      });
    });
  }

  function renderFlashcards(container, cards) {
    container.style.display = 'grid';
    container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(250px, 1fr))';
    container.style.gap = '20px';

    container.innerHTML = cards.map(c => `
      <article class="ud-card sb-flashcard" style="cursor: pointer; min-height: 150px; display: flex; align-items: center; justify-content: center; text-align: center; transition: transform 0.6s; transform-style: preserve-3d;">
        <div class="sb-card-front" style="padding: 20px;">
          <h4 style="font-size: 16px; margin: 0;">${escapeHtml(c.front)}</h4>
        </div>
        <div class="sb-card-back hidden" style="padding: 20px; color: var(--accent-cyan);">
          <p style="margin: 0; font-weight: 600;">${escapeHtml(c.back)}</p>
        </div>
      </article>
    `).join('');

    container.querySelectorAll('.sb-flashcard').forEach(card => {
      card.addEventListener('click', () => {
        card.querySelector('.sb-card-front').classList.toggle('hidden');
        card.querySelector('.sb-card-back').classList.toggle('hidden');
        card.style.borderColor = card.querySelector('.sb-card-front').classList.contains('hidden') ? 'var(--accent-cyan)' : '';
      });
    });
  }

  function resetWorkspace(hideWorkspace = true) {
    $('#sb-setup-form').classList.remove('hidden');
    $('#sb-results').classList.add('hidden');
    $('#sb-content-container').innerHTML = '';
    $('#sb-error').classList.add('hidden');
    if (hideWorkspace) $('#study-buddy-workspace').classList.add('hidden');
  }

  function setLoading(loading) {
    const btn = $('#btnGenerateStudy');
    if (!btn) return;
    btn.disabled = loading;
    btn.textContent = loading ? 'Processing Trajectory...' : 'Generate Materials';
    btn.style.opacity = loading ? '0.7' : '1';
  }

  function showError(msg) {
    const el = $('#sb-error');
    if (!el) return;
    el.textContent = msg;
    el.classList.toggle('hidden', !msg);
  }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
