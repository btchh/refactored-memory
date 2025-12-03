/**
 * Form Protection Module
 * Warns users about unsaved changes before leaving pages
 */

export class FormProtection {
    constructor() {
        this.hasUnsavedChanges = false;
        this.forms = [];
        this.init();
    }

    init() {
        // Track all forms on the page
        this.trackForms();
        
        // Warn before leaving page
        this.setupBeforeUnload();
        
        // Warn before navigation
        this.setupNavigationWarning();
    }

    trackForms() {
        // Find all forms except logout forms
        const forms = document.querySelectorAll('form:not([data-no-protection])');
        
        forms.forEach(form => {
            this.forms.push(form);
            
            // Track input changes
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                // Store initial value
                input.dataset.initialValue = input.value;
                
                // Listen for changes
                input.addEventListener('input', () => {
                    if (input.value !== input.dataset.initialValue) {
                        this.hasUnsavedChanges = true;
                    }
                });
                
                input.addEventListener('change', () => {
                    if (input.value !== input.dataset.initialValue) {
                        this.hasUnsavedChanges = true;
                    }
                });
            });
            
            // Clear flag on successful submit
            form.addEventListener('submit', () => {
                this.hasUnsavedChanges = false;
            });
        });
    }

    setupBeforeUnload() {
        window.addEventListener('beforeunload', (e) => {
            if (this.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    }

    setupNavigationWarning() {
        // Intercept all internal links
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            
            if (!link) return;
            
            // Skip if it's an external link or has data-no-warning
            if (link.hasAttribute('data-no-warning') || 
                link.target === '_blank' ||
                link.href.startsWith('mailto:') ||
                link.href.startsWith('tel:')) {
                return;
            }
            
            // Check if it's an internal navigation
            const currentDomain = window.location.origin;
            if (link.href.startsWith(currentDomain) && this.hasUnsavedChanges) {
                e.preventDefault();
                this.showNavigationWarning(link.href);
            }
        });
    }

    showNavigationWarning(targetUrl) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Unsaved Changes</h3>
                            <p class="text-sm text-gray-500">You have unsaved changes</p>
                        </div>
                    </div>
                    <p class="text-gray-700 mb-6">
                        You have unsaved changes that will be lost if you leave this page. Are you sure you want to continue?
                    </p>
                    <div class="flex gap-3">
                        <button type="button" class="cancel-btn flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                            Stay on Page
                        </button>
                        <button type="button" class="confirm-btn flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl transition-colors">
                            Leave Anyway
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Handle buttons
        modal.querySelector('.cancel-btn').addEventListener('click', () => {
            modal.remove();
        });
        
        modal.querySelector('.confirm-btn').addEventListener('click', () => {
            this.hasUnsavedChanges = false;
            modal.remove();
            window.location.href = targetUrl;
        });
        
        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    reset() {
        this.hasUnsavedChanges = false;
        // Reset initial values
        this.forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.dataset.initialValue = input.value;
            });
        });
    }
}

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.formProtection = new FormProtection();
    });
} else {
    window.formProtection = new FormProtection();
}
