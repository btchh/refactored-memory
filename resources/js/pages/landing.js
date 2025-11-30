/**
 * Landing Page
 * Initializes navigation and scroll behavior
 */

import { LandingNav } from '../modules/landing-nav.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize landing page navigation
    const nav = new LandingNav();
    
    // Expose globally if needed
    window.landingNav = nav;
});
