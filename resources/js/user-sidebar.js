/**
 * FlexiTag Unified User Sidebar & Drawer Interaction Script
 */

window.toggleUserSidebar = function(open) {
    const drawer = document.getElementById('sideDrawer');
    const backdrop = document.getElementById('drawerBackdrop');
    if (!drawer || !backdrop) return;

    if (open) {
        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    } else {
        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
    }
};

// Aliases for backwards compatibility with any existing toggleDrawer calls
window.toggleDrawer = window.toggleUserSidebar;

document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('openDrawerBtn');
    const closeBtn = document.getElementById('closeDrawerBtn');
    const backdrop = document.getElementById('drawerBackdrop');

    if (openBtn) {
        openBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.toggleUserSidebar(true);
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.toggleUserSidebar(false);
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', () => {
            window.toggleUserSidebar(false);
        });
    }

    // Keyboard support: Escape closes drawer
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const drawer = document.getElementById('sideDrawer');
            if (drawer && drawer.classList.contains('is-open')) {
                window.toggleUserSidebar(false);
            }
        }
    });
});
