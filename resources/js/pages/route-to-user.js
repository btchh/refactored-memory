/**
 * Track User Locations Page
 * Initializes the user location tracker for admin
 */

import { UserTracker } from '../modules/user-tracker.js';

document.addEventListener('DOMContentLoaded', () => {
    // Get configuration from window object
    const apiKey = window.geoapifyApiKey || '';
    const fetchUrl = window.routes?.userLocation || '/admin/api/users';

    // Initialize user tracker
    const tracker = new UserTracker({
        mapId: 'map',
        apiKey: apiKey,
        fetchUrl: fetchUrl,
        refreshInterval: 30000 // 30 seconds
    });

    // Expose globally for onclick handlers
    window.userTracker = tracker;
});
