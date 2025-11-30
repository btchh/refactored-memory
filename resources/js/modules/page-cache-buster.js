/**
 * Page Cache Buster
 * Prevents back button from showing cached pages after logout
 */

export function initPageCacheBuster() {
    window.addEventListener('pageshow', function(event) {
        // Check if page was loaded from cache (back/forward button)
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
}

// Auto-initialize
initPageCacheBuster();
