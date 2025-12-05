/**
 * Track Admin/Shop Locations Page
 * Initializes the admin location tracker for users to find branches
 */

import { AdminTracker } from '../modules/admin-tracker.js';

document.addEventListener('DOMContentLoaded', () => {
    // Get configuration from window object
    const apiKey = window.geoapifyApiKey || '';
    const fetchUrl = window.routes?.adminLocation || '/user/api/admins';

    // Initialize admin tracker
    const tracker = new AdminTracker({
        mapId: 'map',
        apiKey: apiKey,
        fetchUrl: fetchUrl,
        refreshInterval: 30000 // 30 seconds
    });

    // Expose globally for onclick handlers
    window.adminTracker = tracker;
});
