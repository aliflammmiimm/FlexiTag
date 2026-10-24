/**
 * FlexiTag User Analytics Interactions
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Drawer open/close toggles
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

    // Keyboard support for drawer
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sideDrawer && sideDrawer.classList.contains('is-open')) {
            toggleDrawer(false);
        }
    });
});
