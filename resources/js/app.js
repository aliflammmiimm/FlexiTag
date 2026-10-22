import { initNavigation } from './navigation.js';
import { initLandingPage } from './landing-page.js';
import { initPublicProfile } from './public-profile.js';

document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    initLandingPage();
    initPublicProfile();
});
