/**
 * FlexiTag User Settings Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Navigation Drawer Toggle
    const openDrawerBtn = document.getElementById('openDrawerBtn');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const drawerBackdrop = document.getElementById('drawerBackdrop');
    const sideDrawer = document.getElementById('sideDrawer');

    function toggleDrawer(open) {
        if (!sideDrawer || !drawerBackdrop) return;
        if (open) {
            sideDrawer.classList.add('is-open');
            drawerBackdrop.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        } else {
            sideDrawer.classList.remove('is-open');
            drawerBackdrop.classList.remove('is-open');
            document.body.style.overflow = '';
        }
    }

    if (openDrawerBtn) {
        openDrawerBtn.addEventListener('click', () => toggleDrawer(true));
    }
    if (closeDrawerBtn) {
        closeDrawerBtn.addEventListener('click', () => toggleDrawer(false));
    }
    if (drawerBackdrop) {
        drawerBackdrop.addEventListener('click', () => toggleDrawer(false));
    }

    // Keyboard support: Escape closes drawer and modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (sideDrawer && sideDrawer.classList.contains('is-open')) {
                toggleDrawer(false);
            }
            closeAllModals();
        }
    });

    // 2. Modals Management
    window.openModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    };

    function closeAllModals() {
        document.querySelectorAll('.settings-modal-overlay.is-active').forEach(m => {
            m.classList.remove('is-active');
        });
        document.body.style.overflow = '';
    }
});
