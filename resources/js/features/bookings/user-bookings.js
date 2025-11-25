/**
 * User Bookings Management
 * Handles booking creation, editing, cancellation, and display for users
 */

class UserBookingsManager {
    constructor() {
        this.bookings = [];
        this.apiEndpoints = {
            list: '/user/bookings/data',
            create: '/user/bookings/create',
            update: (id) => `/user/bookings/${id}/update`,
            cancel: (id) => `/user/bookings/${id}/cancel`,
        };
        this.init();
    }

    /**
     * Initialize the bookings manager
     */
    init() {
        // Only initialize if we're on the bookings page
        if (!document.getElementById('bookingsContainer')) {
            return;
        }

        console.log('[User Bookings] Initializing...');
        this.setupEventListeners();
        this.loadBookings();
    }

    /**
     * Setup event listeners for forms and buttons
     */
    setupEventListeners() {
        // Create booking form
        const createForm = document.getElementById('createBookingForm');
        if (createForm) {
            createForm.addEventListener('submit', (e) => this.handleCreateBooking(e));
        }

        // Edit booking form
        const editForm = document.getElementById('editBookingForm');
        if (editForm) {
            editForm.addEventListener('submit', (e) => this.handleUpdateBooking(e));
        }

        // Make functions globally accessible for inline onclick handlers
        window.editBooking = (id) => this.editBooking(id);
        window.showCancelModal = (id) => this.showCancelModal(id);
        window.confirmCancelBooking = () => this.confirmCancelBooking();
    }

    /**
     * Load bookings from server
     */
    async loadBookings() {
        try {
            this.showLoadingState();

            const response = await fetch(this.apiEndpoints.list, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                this.bookings = data.bookings || [];
                this.renderBookings();
                console.log(`[User Bookings] Loaded ${this.bookings.length} bookings`);
            } else {
                this.showError(data.message || 'Failed to load bookings');
            }
        } catch (error) {
            console.error('[User Bookings] Error loading bookings:', error);
            this.showError('Failed to load bookings. Please try again.');
        }
    }

    /**
     * Show loading state in bookings container
     */
    showLoadingState() {
        const container = document.getElementById('bookingsContainer');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <p class="text-gray-500 mt-2">Loading bookings...</p>
                </div>
            `;
        }
    }

    /**
     * Render bookings list
     */
    renderBookings() {
        const container = document.getElementById('bookingsContainer');
        if (!container) return;

        if (this.bookings.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-500 mt-2">No bookings found. Create your first booking!</p>
                </div>
            `;
            return;
        }

        const bookingsHtml = this.bookings
            .map((booking) => this.renderBookingCard(booking))
            .join('');

        container.innerHTML = bookingsHtml;
    }

    /**
     * Render a single booking card
     */
    renderBookingCard(booking) {
        const startDate = new Date(booking.start_time);
        const endDate = new Date(booking.end_time);
        const duration = Math.round((endDate - startDate) / (1000 * 60)); // minutes

        const statusConfig = {
            pending: { class: 'bg-yellow-100 text-yellow-800', label: 'PENDING' },
            confirmed: { class: 'bg-green-100 text-green-800', label: 'CONFIRMED' },
            cancelled: { class: 'bg-red-100 text-red-800', label: 'CANCELLED' },
            rescheduled: { class: 'bg-blue-100 text-blue-800', label: 'RESCHEDULED' },
        };

        const status = statusConfig[booking.status] || statusConfig.pending;

        return `
            <div class="border-b border-gray-200 py-4 hover:bg-gray-50 transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-800">${this.escapeHtml(booking.title)}</h3>
                            <span class="px-2 py-1 text-xs font-medium rounded ${status.class}">
                                ${status.label}
                            </span>
                        </div>
                        
                        ${booking.description ? `<p class="text-gray-600 text-sm mb-2">${this.escapeHtml(booking.description)}</p>` : ''}
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                            <div>
                                <strong>Date:</strong> ${this.formatDate(startDate)}
                            </div>
                            <div>
                                <strong>Time:</strong> ${this.formatTime(startDate)} - ${this.formatTime(endDate)}
                            </div>
                            <div>
                                <strong>Duration:</strong> ${duration} minutes
                            </div>
                            ${booking.location ? `<div><strong>Location:</strong> ${this.escapeHtml(booking.location)}</div>` : ''}
                        </div>
                        
                        ${this.renderAttendeeInfo(booking)}
                        
                        ${booking.notes ? `<div class="mt-2 text-sm text-gray-500"><strong>Notes:</strong> ${this.escapeHtml(booking.notes)}</div>` : ''}
                    </div>
                    
                    ${booking.status !== 'cancelled' ? this.renderActionButtons(booking.id) : ''}
                </div>
            </div>
        `;
    }

    /**
     * Render attendee information if present
     */
    renderAttendeeInfo(booking) {
        if (!booking.attendee_name && !booking.attendee_email && !booking.attendee_phone) {
            return '';
        }

        const parts = [];
        if (booking.attendee_name) parts.push(this.escapeHtml(booking.attendee_name));
        if (booking.attendee_email) parts.push(`(${this.escapeHtml(booking.attendee_email)})`);
        if (booking.attendee_phone) parts.push(`- ${this.escapeHtml(booking.attendee_phone)}`);

        return `
            <div class="mt-2 text-sm text-gray-600">
                <strong>Attendee:</strong> ${parts.join(' ')}
            </div>
        `;
    }

    /**
     * Render action buttons for a booking
     */
    renderActionButtons(bookingId) {
        return `
            <div class="flex space-x-2 ml-4">
                <button onclick="editBooking(${bookingId})" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Edit
                </button>
                <button onclick="showCancelModal(${bookingId})" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500">
                    Cancel
                </button>
            </div>
        `;
    }

    /**
     * Handle create booking form submission
     */
    async handleCreateBooking(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        // Client-side validation
        if (!this.validateBookingData(data)) {
            return;
        }

        try {
            const response = await fetch(this.apiEndpoints.create, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(result.message || 'Booking created successfully');
                this.hideModal('createBookingModal');
                form.reset();
                await this.loadBookings();
            } else {
                this.showError(result.message || 'Failed to create booking');
            }
        } catch (error) {
            console.error('[User Bookings] Error creating booking:', error);
            this.showError('Failed to create booking. Please try again.');
        }
    }

    /**
     * Edit booking - populate form and show modal
     */
    editBooking(bookingId) {
        const booking = this.bookings.find((b) => b.id === bookingId);
        if (!booking) {
            this.showError('Booking not found');
            return;
        }

        // Populate form fields
        document.getElementById('edit_booking_id').value = booking.id;
        document.getElementById('edit_title').value = booking.title;
        document.getElementById('edit_description').value = booking.description || '';
        document.getElementById('edit_start_time').value = this.formatDateTimeLocal(booking.start_time);
        document.getElementById('edit_end_time').value = this.formatDateTimeLocal(booking.end_time);
        document.getElementById('edit_location').value = booking.location || '';
        document.getElementById('edit_attendee_name').value = booking.attendee_name || '';
        document.getElementById('edit_attendee_email').value = booking.attendee_email || '';
        document.getElementById('edit_attendee_phone').value = booking.attendee_phone || '';
        document.getElementById('edit_notes').value = booking.notes || '';

        this.showModal('editBookingModal');
    }

    /**
     * Handle update booking form submission
     */
    async handleUpdateBooking(event) {
        event.preventDefault();

        const form = event.target;
        const bookingId = document.getElementById('edit_booking_id').value;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        delete data.booking_id;

        // Client-side validation
        if (!this.validateBookingData(data)) {
            return;
        }

        try {
            const response = await fetch(this.apiEndpoints.update(bookingId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(result.message || 'Booking updated successfully');
                this.hideModal('editBookingModal');
                await this.loadBookings();
            } else {
                this.showError(result.message || 'Failed to update booking');
            }
        } catch (error) {
            console.error('[User Bookings] Error updating booking:', error);
            this.showError('Failed to update booking. Please try again.');
        }
    }

    /**
     * Show cancel booking confirmation modal
     */
    showCancelModal(bookingId) {
        const booking = this.bookings.find((b) => b.id === bookingId);
        if (!booking) {
            this.showError('Booking not found');
            return;
        }

        document.getElementById('cancel_booking_id').value = booking.id;
        document.getElementById('cancel_booking_title').textContent = booking.title;
        document.getElementById('cancel_booking_date').textContent = this.formatDate(
            new Date(booking.start_time)
        );

        this.showModal('cancelBookingModal');
    }

    /**
     * Confirm and execute booking cancellation
     */
    async confirmCancelBooking() {
        const bookingId = document.getElementById('cancel_booking_id').value;

        try {
            const response = await fetch(this.apiEndpoints.cancel(bookingId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json',
                },
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccess(result.message || 'Booking cancelled successfully');
                this.hideModal('cancelBookingModal');
                await this.loadBookings();
            } else {
                this.showError(result.message || 'Failed to cancel booking');
            }
        } catch (error) {
            console.error('[User Bookings] Error cancelling booking:', error);
            this.showError('Failed to cancel booking. Please try again.');
        }
    }

    /**
     * Validate booking data before submission
     */
    validateBookingData(data) {
        if (!data.title || data.title.trim() === '') {
            this.showError('Title is required');
            return false;
        }

        if (!data.start_time) {
            this.showError('Start time is required');
            return false;
        }

        if (!data.end_time) {
            this.showError('End time is required');
            return false;
        }

        const startTime = new Date(data.start_time);
        const endTime = new Date(data.end_time);
        const now = new Date();

        if (startTime < now) {
            this.showError('Start time must be in the future');
            return false;
        }

        if (endTime <= startTime) {
            this.showError('End time must be after start time');
            return false;
        }

        if (data.attendee_email && !this.isValidEmail(data.attendee_email)) {
            this.showError('Please enter a valid email address');
            return false;
        }

        return true;
    }

    /**
     * Validate email format
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show modal
     */
    showModal(modalId) {
        if (window.showModal) {
            window.showModal(modalId);
        } else {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }
    }

    /**
     * Hide modal
     */
    hideModal(modalId) {
        if (window.hideModal) {
            window.hideModal(modalId);
        } else {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    }

    /**
     * Show success notification
     */
    showSuccess(message) {
        if (window.notificationManager) {
            window.notificationManager.success(message);
        } else if (window.showNotification) {
            window.showNotification('success', message);
        } else {
            alert(message);
        }
    }

    /**
     * Show error notification
     */
    showError(message) {
        if (window.notificationManager) {
            window.notificationManager.error(message);
        } else if (window.showNotification) {
            window.showNotification('error', message);
        } else {
            alert(message);
        }
    }

    /**
     * Get CSRF token from meta tag
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.content : '';
    }

    /**
     * Format date for display
     */
    formatDate(date) {
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
    }

    /**
     * Format time for display
     */
    formatTime(date) {
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    /**
     * Format datetime for datetime-local input
     */
    formatDateTimeLocal(dateString) {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new UserBookingsManager();
    });
} else {
    new UserBookingsManager();
}

export default UserBookingsManager;
