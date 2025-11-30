/**
 * Modal Helper Functions
 * Simple show/hide functions for modals
 */

export function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
    }
}

export function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Expose globally for onclick handlers
window.showModal = showModal;
window.hideModal = hideModal;
