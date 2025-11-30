/**
 * Session Notifications Handler
 * Displays flash messages from Laravel session
 */

export function initSessionNotifications() {
    // Wait for notification manager to be ready
    function showNotification(type, message, duration) {
        const manager = window.notificationManager;
        if (manager) {
            manager[type](message, duration);
        } else {
            // Retry if manager not ready yet
            setTimeout(() => showNotification(type, message, duration), 100);
        }
    }

    // Check for session messages in window object
    if (window.sessionMessages) {
        const { success, error, warning, info } = window.sessionMessages;
        
        if (success) {
            showNotification('success', success, 5000);
        }
        
        if (error) {
            showNotification('error', error, 0); // 0 = no auto-dismiss
        }
        
        if (warning) {
            showNotification('warning', warning, 5000);
        }
        
        if (info) {
            showNotification('info', info, 5000);
        }
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSessionNotifications);
} else {
    initSessionNotifications();
}
