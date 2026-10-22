/**
 * FlexiTag Landing Page Interactivity
 * Interactive 4-Mode Switcher Demo
 */

export function initLandingPage() {
    const tabs = document.querySelectorAll('.ft-mode-tab');
    const panes = document.querySelectorAll('.ft-mode-content-pane');

    if (tabs.length === 0 || panes.length === 0) {
        return;
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetMode = tab.getAttribute('data-mode');

            // Deactivate all tabs & panes
            tabs.forEach(t => {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            panes.forEach(p => p.classList.remove('is-active'));

            // Activate current tab & target pane
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');

            const targetPane = document.getElementById(`ft-pane-${targetMode}`);
            if (targetPane) {
                targetPane.classList.add('is-active');
            }
        });
    });
}
