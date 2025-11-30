/**
 * Notification System
 * Handles display, auto-dismiss, and user interactions for notifications
 */

class NotificationManager {
    constructor(options = {}) {
        this.options = {
            autoCloseDuration: 5000, // 5 seconds
            animationDuration: 300,
            ...options,
        };
        this.init();
    }

    init() {
        this.setupNotifications();
        this.setupCloseButtons();
    }

    setupNotifications() {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach((notification) => {
            this.setupNotification(notification);
        });
    }

    setupNotification(notification) {
        const type = notification.dataset.type;
        
        // Auto-close after duration (except for errors and warnings)
        if (type !== 'error' && type !== 'warning') {
            setTimeout(() => {
                this.closeNotification(notification);
            }, this.options.autoCloseDuration);
        }

        // Add keyboard support (ESC to close)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeNotification(notification);
            }
        });
    }

    setupCloseButtons() {
        const closeButtons = document.querySelectorAll('.notification-close');
        closeButtons.forEach((button) => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const notification = button.closest('.notification');
                this.closeNotification(notification);
            });
        });
    }

    closeNotification(notification) {
        if (!notification) return;

        notification.classList.remove('animate-slide-in');
        notification.classList.add('animate-fade-out');

        setTimeout(() => {
            notification.remove();
        }, this.options.animationDuration);
    }

    /**
     * Show a custom notification
     */
    show(message, type = 'info', duration = null) {
        const container = document.getElementById('notifications-container') || document.body;
        const notification = document.createElement('div');
        notification.dataset.type = type;
        notification.setAttribute('role', 'alert');

        const icons = {
            success: '<svg class="h-5 w-5 text-success" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
            error: '<svg class="h-5 w-5 text-error" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>',
            warning: '<svg class="h-5 w-5 text-warning" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
            info: '<svg class="h-5 w-5 text-info" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>',
        };

        const bgClasses = {
            success: 'bg-success/5',
            error: 'bg-error/5',
            warning: 'bg-warning/5',
            info: 'bg-info/5',
        };

        const borderClasses = {
            success: 'border-l-success',
            error: 'border-l-error',
            warning: 'border-l-warning',
            info: 'border-l-info',
        };

        const textClasses = {
            success: 'text-success',
            error: 'text-error',
            warning: 'text-warning',
            info: 'text-info',
        };

        notification.className = `alert ${bgClasses[type]} border-l-4 ${borderClasses[type]} rounded-lg shadow-md p-4 max-w-md pointer-events-auto animate-slide-in`;
        notification.innerHTML = `
            <div class="alert-icon flex-shrink-0 mt-0.5">
                ${icons[type]}
            </div>
            <div class="alert-content flex-1">
                <p class="text-sm font-medium ${textClasses[type]}">${message}</p>
            </div>
            <button type="button" class="alert-close notification-close flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded" aria-label="Close">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        `;

        container.appendChild(notification);

        // Setup close button
        const closeButton = notification.querySelector('.notification-close');
        closeButton.addEventListener('click', () => {
            this.closeNotification(notification);
        });

        // Auto-close
        const closeDuration = duration || (type === 'error' ? 0 : this.options.autoCloseDuration);
        if (closeDuration > 0) {
            setTimeout(() => {
                this.closeNotification(notification);
            }, closeDuration);
        }

        return notification;
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration || 0);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }

    /**
     * Clear all notifications
     */
    clearAll() {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach((notification) => {
            this.closeNotification(notification);
        });
    }
}

// Initialize notification manager on page load
let notificationManager;

function initNotifications() {
    notificationManager = new NotificationManager();
    window.notificationManager = notificationManager;
    console.log('[Notifications] Manager initialized');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotifications);
} else {
    initNotifications();
}

// Export for use in other modules
export { NotificationManager };
export default notificationManager;
