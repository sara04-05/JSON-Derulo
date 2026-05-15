    <button type="button" class="fab-help" title="Help" aria-label="Help">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
    </button>

    <script>
        window.__ELEVURA_INITIAL__ = <?= json_encode($initialPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        window.__ELEVURA_AUTH_PROMPT__ = <?= json_encode($authPrompt) ?>;
    </script>
    <script src="auth-state.js"></script>
    <script src="auth-ui.js"></script>
    <script src="protected-ui.js"></script>
    <script src="user-dashboard.js"></script>
    <script src="dashboard-interactions.js"></script>
    <script>
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            faqItem.classList.toggle('active');
        }
        document.addEventListener('DOMContentLoaded', function () {
            if (window.__ELEVURA_AUTH_PROMPT__ && window.ElevUraAuthUI?.openAuth) {
                window.ElevUraAuthUI.openAuth(window.__ELEVURA_AUTH_PROMPT__);
            }
        });
    </script>
