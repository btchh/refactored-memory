/**
 * Reusable Cancel Booking Module
 * Provides a consistent cancel booking modal for both admin and user interfaces
 */

// Predefined cancellation reasons for different user types
const CANCEL_REASONS = {
    admin: [
        'Customer request',
        'Unable to fulfill order',
        'Scheduling conflict',
        'Service unavailable',
        'Weather conditions',
        'Staff shortage',
        'Other'
    ],
    user: [
        'Change of plans',
        'Found another service',
        'Schedule conflict',
        'No longer needed',
        'Financial reasons',
        'Other'
    ]
};

/**
 * Show cancel booking modal
 * @param {number} bookingId - The booking ID to cancel
 * @param {object} options - Configuration options
 * @param {string} options.type - 'admin' or 'user'
 * @param {string} options.cancelUrl - URL to send cancel request
 * @param {string} options.csrfToken - CSRF token for the request
 * @param {function} options.onSuccess - Callback on successful cancellation
 * @param {function} options.onError - Callback on error
 */
export function showCancelModal(bookingId, options = {}) {
    const {
        type = 'user',
        cancelUrl,
        csrfToken,
        onSuccess = () => {},
        onError = () => {}
    } = options;

    const reasons = CANCEL_REASONS[type] || CANCEL_REASONS.user;
    const title = type === 'admin' ? 'Cancel Customer Booking' : 'Cancel Your Booking';
    const subtitle = type === 'admin' 
        ? 'The customer will be notified of this cancellation'
        : 'This action cannot be undone';

    // Create modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform animate-scale-in">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">${title}</h3>
                    <p class="text-sm text-gray-500">${subtitle}</p>
                </div>
            </div>
            
            <p class="text-gray-700 mb-4">Are you sure you want to cancel this booking?</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Why are you cancelling?</label>
                <select id="cancel-reason-${bookingId}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white text-gray-900">
                    ${reasons.map((reason, index) => `
                        <option value="${reason}" ${index === 0 ? 'selected' : ''}>${reason}</option>
                    `).join('')}
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Additional details (optional)</label>
                <textarea id="cancel-notes-${bookingId}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none bg-white text-gray-900" rows="2" placeholder="Add any additional information..."></textarea>
            </div>
            
            <div class="flex gap-3">
                <button type="button" class="cancel-modal-btn flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Keep Booking
                </button>
                <button type="button" class="confirm-cancel-btn flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-xl transition-colors">
                    Yes, Cancel
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle cancel button (close modal)
    modal.querySelector('.cancel-modal-btn').addEventListener('click', () => {
        modal.remove();
    });
    
    // Handle confirm cancel button
    modal.querySelector('.confirm-cancel-btn').addEventListener('click', async () => {
        const reasonSelect = document.getElementById(`cancel-reason-${bookingId}`);
        const notesTextarea = document.getElementById(`cancel-notes-${bookingId}`);
        const confirmBtn = modal.querySelector('.confirm-cancel-btn');
        
        if (!reasonSelect || !confirmBtn) return;
        
        const reason = reasonSelect.value;
        const notes = notesTextarea ? notesTextarea.value.trim() : '';
        const fullReason = notes ? `${reason}: ${notes}` : reason;
        
        // Update button state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = `
            <svg class="w-4 h-4 animate-spin mr-2 inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Cancelling...
        `;
        
        try {
            const response = await fetch(cancelUrl.replace('__ID__', bookingId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: fullReason })
            });
            
            const result = await response.json();
            
            // Remove modal
            modal.remove();
            
            if (result.success) {
                // Show success toast
                showToast('success', result.message || 'Booking cancelled successfully');
                onSuccess(result);
            } else {
                showToast('error', result.message || 'Failed to cancel booking');
                onError(result);
            }
        } catch (error) {
            console.error('Cancel booking error:', error);
            modal.remove();
            showToast('error', 'Failed to cancel booking. Please try again.');
            onError(error);
        }
    });
    
    // Close on backdrop click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
    
    // Close on escape key
    const escapeHandler = (e) => {
        if (e.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', escapeHandler);
        }
    };
    document.addEventListener('keydown', escapeHandler);
}

/**
 * Show toast notification
 * @param {string} type - 'success' or 'error'
 * @param {string} message - Message to display
 */
function showToast(type, message) {
    // Try to use existing toast system first
    if (window.Toast) {
        if (type === 'success') {
            window.Toast.success(message);
        } else {
            window.Toast.error(message);
        }
        return;
    }
    
    // Fallback to custom toast
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-[10000] transform transition-all animate-slide-in`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Make it available globally for inline onclick handlers
if (typeof window !== 'undefined') {
    window.showCancelModal = showCancelModal;
    console.log('Cancel booking module loaded successfully');
}