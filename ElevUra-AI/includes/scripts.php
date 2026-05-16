 

    <script>
        window.__ELEVURA_INITIAL__ = <?= json_encode($initialPayload, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        window.__ELEVURA_AUTH_PROMPT__ = <?= json_encode($authPrompt) ?>;
    </script>
    <script src="auth-state.js"></script>
    <script src="auth-ui.js"></script>
    <script src="protected-ui.js"></script>
    <?php if (($pageSlug ?? 'home') === 'user-dashboard'): ?>
    <script src="user-dashboard.js"></script>
    <?php elseif (($pageSlug ?? 'home') === 'research-assistant'): ?>
        <script src="dashboard-interactions.js"></script>
        <script src="research-assistant.js"></script>
    <?php elseif (($pageSlug ?? 'home') === 'study-buddy'): ?>
        <script src="dashboard-interactions.js"></script>
        <script src="study-buddy.js"></script>
<?php else: ?>
    <script src="dashboard-interactions.js"></script>
    <script>
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            faqItem.classList.toggle('active');
        }
    </script>
<?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.__ELEVURA_AUTH_PROMPT__ && window.ElevUraAuthUI?.openAuth) {
                window.ElevUraAuthUI.openAuth(window.__ELEVURA_AUTH_PROMPT__);
            }
        });
    </script>
