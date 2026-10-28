/**
 * FlexiTag User Settings Interactions
 * Manages modal dialogues and keyboard accessibility.
 * Note: Navigation drawer is managed by user-sidebar.js.
 */

document.addEventListener('DOMContentLoaded', () => {
    window.openModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';

            // Auto-focus first interactive control
            const firstInput = modal.querySelector('input:not([type="hidden"]), button.btn-secondary-action');
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 60);
            }
        }
    };

    window.closeModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('is-active');
            // Only restore scroll if no other modals are active
            const otherActive = document.querySelector('.settings-modal-overlay.is-active');
            if (!otherActive) {
                document.body.style.overflow = '';
            }
        }
    };

    function closeAllModals() {
        document.querySelectorAll('.settings-modal-overlay.is-active').forEach(m => {
            m.classList.remove('is-active');
        });
        document.body.style.overflow = '';
    }

    // Keyboard support: Escape key closes active modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
});
