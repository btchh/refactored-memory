/**
 * Centralized Toast Notification System
 * Usage: Toast.show('Message', 'success|error|warning|info')
 */

const Toast = {
    container: null,
    
    config: {
        success: {
            bg: 'bg-green-500',
            icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />`,
            title: 'Success'
        },
        error: {
            bg: 'bg-red-500',
            icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />`,
            title: 'Error'
        },
        warning: {
            bg: 'bg-yellow-500',
            icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />`,
            title: 'Warning'
        },
        info: {
            bg: 'bg-blue-500',
            icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`,
            title: 'Info'
        }
    },

    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            // Create container if it doesn't exist
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed top-20 right-4 z-[200] flex flex-col gap-3 max-w-sm w-full pointer-events-none';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'info', duration = 5000) {
        if (!this.container) this.init();
        
        const cfg = this.config[type] || this.config.info;
        const id = 'toast-' + Date.now();
        
        const toast = document.createElement('div');
        toast.id = id;
        toast.className = `pointer-events-auto transform translate-x-full opacity-0 transition-all duration-300 ease-out`;
        toast.innerHTML = `
            <div class="flex items-start gap-3 p-4 rounded-xl shadow-2xl ${cfg.bg} text-white backdrop-blur-sm">
                <div class="flex-shrink-0 w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${cfg.icon}
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm">${cfg.title}</p>
                    <p class="text-sm text-white/90 mt-0.5">${message}</p>
                </div>
                <button onclick="Toast.dismiss('${id}')" class="flex-shrink-0 p-1 hover:bg-white/20 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="h-1 mt-1 rounded-full bg-white/30 overflow-hidden">
                <div class="h-full bg-white/60 rounded-full toast-progress" style="animation: toast-progress ${duration}ms linear forwards"></div>
            </div>
        `;
        
        this.container.appendChild(toast);
        
        // Trigger animation
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });
        
        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => this.dismiss(id), duration);
        }
        
        return id;
    },

    dismiss(id) {
        const toast = document.getElementById(id);
        if (!toast) return;
        
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    },

    success(message, duration) {
        return this.show(message, 'success', duration);
    },

    error(message, duration) {
        return this.show(message, 'error', duration);
    },

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    },

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
};

// Add CSS for progress bar animation
const style = document.createElement('style');
style.textContent = `
    @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0%; }
    }
`;
document.head.appendChild(style);

// Expose globally IMMEDIATELY (before DOMContentLoaded)
window.Toast = Toast;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Toast.init());
} else {
    Toast.init();
}

export default Toast;
