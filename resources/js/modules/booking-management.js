/**
 * Booking Management Module
 * Handles booking list display and management actions (reschedule, cancel, status change)
 */

import { api } from './api.js';

export class BookingManagement {
    constructor(options = {}) {
        this.userId = options.userId || null;
        this.onBookingUpdate = options.onBookingUpdate || (() => {});
    }

    async loadUserBookings(userId) {
        this.userId = userId;
        
        try {
            const data = await api.get(`/admin/api/bookings/user/${userId}`);
            
            if (data.success && data.bookings.length > 0) {
                this.displayBookings(data.bookings);
            } else {
                this.displayEmptyState();
            }
        } catch (error) {
            console.error('Error loading bookings:', error);
            this.displayEmptyState();
        }
    }

    displayBookings(bookings) {
        const container = document.getElementById('user-bookings-list');
        const section = document.getElementById('user-bookings-section');
        
        if (!container || !section) return;

        container.innerHTML = bookings.map(booking => `
            <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-md transition-shadow ${booking.status === 'cancelled' ? 'opacity-60' : ''}">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-2">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">${booking.datetime}</p>
                        <p class="text-sm text-gray-600 mt-1">${booking.item_type} - ₱${booking.total}</p>
                        <p class="text-sm text-gray-600">${booking.services}</p>
                        ${booking.products ? `<p class="text-sm text-gray-600">${booking.products}</p>` : ''}
                    </div>
                    <span class="badge ${this.getStatusBadgeClass(booking.status)} self-start">${booking.status.replace('_', ' ')}</span>
                </div>
                ${booking.is_upcoming && booking.status !== 'cancelled' ? `
                    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-100">
                        <button onclick="window.bookingManager.openRescheduleModal(${booking.id})" class="btn btn-sm btn-outline flex-1 sm:flex-none">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Reschedule
                        </button>
                        <button onclick="window.bookingManager.cancelBooking(${booking.id})" class="btn btn-sm btn-outline text-error border-error hover:bg-error hover:text-white flex-1 sm:flex-none">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </button>
                        <select onchange="window.bookingManager.changeStatus(${booking.id}, this.value)" class="select select-sm select-bordered flex-1 sm:flex-none">
                            <option value="">Change Status</option>
                            <option value="pending" ${booking.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="in_progress" ${booking.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                            <option value="completed" ${booking.status === 'completed' ? 'selected' : ''}>Completed</option>
                        </select>
                    </div>
                ` : ''}
            </div>
        `).join('');

        section.classList.remove('hidden');
    }

    displayEmptyState() {
        const container = document.getElementById('user-bookings-list');
        const section = document.getElementById('user-bookings-section');
        
        if (container) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">No bookings found</p>';
        }
        
        if (section) {
            section.classList.add('hidden');
        }
    }

    getStatusBadgeClass(status) {
        const classes = {
            'pending': 'badge-warning',
            'in_progress': 'badge-info',
            'completed': 'badge-success',
            'cancelled': 'badge-error'
        };
        return classes[status] || 'badge-neutral';
    }

    async cancelBooking(id) {
        if (!confirm('Are you sure you want to cancel this booking?')) return;
        
        const reason = prompt('Cancellation reason (optional):');
        
        try {
            const data = await api.delete(`/admin/bookings/${id}`, { reason });
            
            if (data.success) {
                alert('Booking cancelled successfully');
                this.loadUserBookings(this.userId);
                this.onBookingUpdate();
            }
        } catch (error) {
            console.error('Error cancelling booking:', error);
            alert('Failed to cancel booking');
        }
    }

    async changeStatus(id, status) {
        if (!status) return;
        
        try {
            const data = await api.patch(`/admin/bookings/${id}/status`, { status });
            
            if (data.success) {
                alert('Status updated successfully');
                this.loadUserBookings(this.userId);
                this.onBookingUpdate();
            }
        } catch (error) {
            console.error('Error updating status:', error);
            alert('Failed to update status');
        }
    }

    openRescheduleModal(id) {
        const modal = document.getElementById('reschedule-modal');
        const bookingIdInput = document.getElementById('reschedule-booking-id');
        
        if (bookingIdInput) {
            bookingIdInput.value = id;
        }
        
        if (modal && modal.showModal) {
            modal.showModal();
        }
    }

    async submitReschedule(id, date, time) {
        try {
            const data = await api.post(`/admin/bookings/${id}/reschedule`, {
                booking_date: date,
                booking_time: time
            });
            
            if (data.success) {
                alert('Booking rescheduled successfully');
                
                const modal = document.getElementById('reschedule-modal');
                if (modal && modal.close) {
                    modal.close();
                }
                
                this.loadUserBookings(this.userId);
                this.onBookingUpdate();
            }
        } catch (error) {
            console.error('Error rescheduling:', error);
            alert('Failed to reschedule booking');
        }
    }

    setupRescheduleForm() {
        const form = document.getElementById('reschedule-form');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const id = document.getElementById('reschedule-booking-id')?.value;
            const date = document.getElementById('reschedule-date')?.value;
            const time = document.getElementById('reschedule-time')?.value;
            
            if (id && date && time) {
                await this.submitReschedule(id, date, time);
            }
        });
    }

    init() {
        this.setupRescheduleForm();
        
        // Make available globally for onclick handlers
        window.bookingManager = this;
    }
}
