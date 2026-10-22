/**
 * FlexiTag Public Navigation Interactivity
 */

export function initNavigation() {
    const hamburgerBtn = document.getElementById('ft-hamburger-btn');
    const drawer = document.getElementById('ft-drawer');
    const drawerBackdrop = document.getElementById('ft-drawer-backdrop');
    const drawerCloseBtn = document.getElementById('ft-drawer-close-btn');

    if (!hamburgerBtn || !drawer || !drawerBackdrop) {
        return;
    }

    function openDrawer() {
        drawer.classList.add('is-open');
        drawerBackdrop.classList.add('is-open');
        hamburgerBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('is-open');
        drawerBackdrop.classList.remove('is-open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    hamburgerBtn.addEventListener('click', openDrawer);

    if (drawerCloseBtn) {
        drawerCloseBtn.addEventListener('click', closeDrawer);
    }

    drawerBackdrop.addEventListener('click', closeDrawer);

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    // Close drawer when any drawer nav-link is clicked
    drawer.querySelectorAll('.ft-nav-link').forEach(link => {
        link.addEventListener('click', closeDrawer);
    });
}
