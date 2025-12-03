/**
 * Admin Booking Page
 * Main entry point for admin booking interface
 */

import { Calendar } from '../modules/calendar.js';
import { BookingForm } from '../modules/booking-form.js';
import { UserSearch } from '../modules/user-search.js';
import { BookingManagement } from '../modules/booking-management.js';
import { TimeSlots } from '../modules/time-slots.js';

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Get data from window object
    const data = window.bookingData;
    if (!data) {
        console.error('Booking data not found');
        return;
    }

    const { services, products, routes, csrf } = data;

    // Initialize Time Slots first (needed by calendar)
    const timeSlots = new TimeSlots({
        selectId: 'booking_time',
        slotsUrl: routes.slots
    });

    // Initialize Booking Management first (needed by user search)
    const bookingManagement = new BookingManagement({
        bookingsUrl: routes.bookings,
        userBookingsUrl: routes.userBookings,
        csrf: csrf,
        onBookingUpdate: () => {
            // Refresh calendar or other UI elements if needed
        }
    });
    bookingManagement.init();

    // Initialize Calendar
    const calendar = new Calendar('calendar-container', {
        countsUrl: routes.bookingCounts,
        onDateSelect: async (date) => {
            const dateInput = document.getElementById('booking_date');
            if (dateInput) dateInput.value = date;
            
            // Load bookings for this date
            await loadDateBookings(date);
            
            // Load time slots
            timeSlots.loadSlots(date);
        }
    });
    calendar.init();

    // Load bookings for a specific date
    async function loadDateBookings(date) {
        const dateBookingsSection = document.getElementById('date-bookings-section');
        const dateBookingsList = document.getElementById('date-bookings-list');
        const selectedDateDisplay = document.getElementById('selected-date-display');
        const bookingForm = document.getElementById('booking-form');
        const emptyState = document.getElementById('empty-state');
        const bookingsCountBadge = document.getElementById('bookings-count-badge');
        
        // Show loading state
        if (dateBookingsList) {
            dateBookingsList.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    <span class="ml-3 text-gray-600">Loading bookings...</span>
                </div>
            `;
        }
        
        try {
            const response = await fetch(`${routes.bookingsByDate}?date=${date}`);
            const data = await response.json();
            
            if (!data.success || !dateBookingsSection || !dateBookingsList) return;
            
            // Smooth transition: hide empty state
            if (emptyState) {
                emptyState.style.opacity = '0';
                setTimeout(() => emptyState.classList.add('hidden'), 300);
            }
            
            // Update date display with animation
            if (selectedDateDisplay) {
                const dateObj = new Date(date + 'T00:00:00');
                selectedDateDisplay.textContent = dateObj.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            }
            
            // Update bookings count
            if (bookingsCountBadge) {
                bookingsCountBadge.textContent = data.bookings.length;
            }
            
            // Show bookings section with animation
            dateBookingsSection.classList.remove('hidden');
            setTimeout(() => {
                dateBookingsSection.style.opacity = '1';
            }, 10);
            
            // Always hide form initially - user must click "Add New Booking"
            if (bookingForm) bookingForm.classList.add('hidden');
            
            if (data.bookings.length === 0) {
                // No bookings - show empty message with icon
                dateBookingsList.innerHTML = `
                    <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">No Bookings Yet</h4>
                        <p class="text-gray-600 mb-4">This date is available for new bookings</p>
                        <button onclick="showBookingForm()" class="btn btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create First Booking
                        </button>
                    </div>
                `;
            } else {
                // Has bookings - show list with stagger animation
                dateBookingsList.innerHTML = data.bookings.map((booking, index) => `
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-primary-300 transition-all duration-200 bg-white animate-slide-in" style="animation-delay: ${index * 50}ms">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex items-center gap-2 bg-gray-100 px-3 py-1 rounded-full">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        <span class="font-bold text-gray-900 text-sm">#${booking.id}</span>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full ${getStatusClass(booking.status)} shadow-sm">
                                        ${booking.status.replace('_', ' ').toUpperCase()}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-sm text-gray-900 font-medium">${booking.user.name}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">${booking.time}</span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-sm text-gray-700 capitalize">${booking.item_type}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <span class="text-sm text-gray-600">${booking.services || 'No services'}</span>
                                    </div>
                                    <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                                        <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                        <span class="text-base font-bold text-success">₱${parseFloat(booking.total).toFixed(2)}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="viewBooking(${booking.id})" class="btn btn-sm btn-info">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </button>
                                ${booking.is_upcoming ? `
                                    <button onclick="rescheduleBooking(${booking.id})" class="btn btn-sm btn-primary">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Reschedule
                                    </button>
                                    <button onclick="cancelBooking(${booking.id})" class="btn btn-sm btn-error">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Cancel
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        } catch (error) {
            console.error('Failed to load date bookings:', error);
        }
    }
    
    // Helper function for status badge colors
    function getStatusClass(status) {
        const classes = {
            'pending': 'bg-yellow-100 text-yellow-700',
            'in_progress': 'bg-blue-100 text-blue-700',
            'completed': 'bg-green-100 text-green-700',
            'cancelled': 'bg-red-100 text-red-700'
        };
        return classes[status] || 'bg-gray-100 text-gray-700';
    }
    
    // Function to show booking form and hide date bookings section
    function showBookingForm() {
        const bookingForm = document.getElementById('booking-form');
        const dateBookingsSection = document.getElementById('date-bookings-section');
        
        if (bookingForm) {
            bookingForm.classList.remove('hidden');
            // Small delay for smooth animation
            setTimeout(() => {
                bookingForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
        
        // Hide the date bookings section when form is shown
        if (dateBookingsSection) {
            dateBookingsSection.style.opacity = '0';
            setTimeout(() => {
                dateBookingsSection.classList.add('hidden');
            }, 300);
        }
    }
    
    // Make function global for onclick handlers
    window.showBookingForm = showBookingForm;
    
    // Show add form button handler
    const showAddFormBtn = document.getElementById('show-add-form-btn');
    if (showAddFormBtn) {
        showAddFormBtn.addEventListener('click', showBookingForm);
    }
    
    // Make functions global for onclick handlers
    window.viewBooking = async (id) => {
        try {
            const response = await fetch(routes.bookingDetails.replace('__ID__', id));
            const result = await response.json();

            if (result.success) {
                const booking = result.booking;
                const viewContent = document.getElementById('view-booking-content');
                
                viewContent.innerHTML = `
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Booking ID</p>
                            <p class="text-base font-semibold text-gray-900">#${booking.id}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Status</p>
                            <p class="text-base font-semibold text-gray-900">${booking.status}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">Customer</p>
                            <p class="text-base font-semibold text-gray-900">${booking.customer.name}</p>
                            <p class="text-sm text-gray-500">${booking.customer.email}</p>
                            <p class="text-sm text-gray-500">${booking.customer.phone}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Date</p>
                            <p class="text-base font-semibold text-gray-900">${booking.booking_date}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Time</p>
                            <p class="text-base font-semibold text-gray-900">${booking.booking_time}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">Pickup Address</p>
                            <p class="text-base text-gray-900">${booking.pickup_address}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Item Type</p>
                            <p class="text-base font-semibold text-gray-900">${booking.item_type}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Weight</p>
                            <p class="text-base font-semibold text-gray-900">${booking.weight}</p>
                        </div>
                        ${booking.services.length > 0 ? `
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-2">Services</p>
                            <ul class="space-y-1">
                                ${booking.services.map(s => `<li class="text-sm text-gray-900">• ${s.name} - ${s.price}</li>`).join('')}
                            </ul>
                        </div>
                        ` : ''}
                        ${booking.products.length > 0 ? `
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600 mb-2">Products</p>
                            <ul class="space-y-1">
                                ${booking.products.map(p => `<li class="text-sm text-gray-900">• ${p.name} - ${p.price}</li>`).join('')}
                            </ul>
                        </div>
                        ` : ''}
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-600">Notes</p>
                            <p class="text-base text-gray-900">${booking.notes}</p>
                        </div>
                        <div class="col-span-2 pt-4 border-t">
                            <p class="text-sm font-medium text-gray-600">Total Price</p>
                            <p class="text-2xl font-bold text-success">${booking.total_price}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Created: ${booking.created_at}</p>
                        </div>
                        <div class="col-span-2 pt-4 border-t">
                            <button onclick="editWeightFromView(${booking.id}, '${booking.weight}')" class="btn btn-warning w-full">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                                Update Weight
                            </button>
                        </div>
                    </div>
                `;
                
                document.getElementById('view-booking-modal').showModal();
            } else {
                window.Toast?.error('Failed to load booking details');
            }
        } catch (error) {
            console.error('Error:', error);
            window.Toast?.error('Failed to load booking details');
        }
    };
    
    window.rescheduleBooking = (id) => {
        bookingManagement.openRescheduleModal(id);
    };
    
    window.editWeightFromView = (id, currentWeight) => {
        document.getElementById('view-booking-modal').close();
        document.getElementById('edit-weight-booking-id').value = id;
        document.getElementById('edit-booking-weight').value = currentWeight.replace(' kg', '') || '';
        document.getElementById('edit-weight-modal').showModal();
    };

    // Handle weight form submission
    const editWeightForm = document.getElementById('edit-weight-form');
    if (editWeightForm) {
        editWeightForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const bookingId = document.getElementById('edit-weight-booking-id').value;
            const weight = document.getElementById('edit-booking-weight').value;

            try {
                const response = await fetch(routes.updateWeight.replace('__ID__', bookingId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': bookingData.csrf
                    },
                    body: JSON.stringify({ weight: weight })
                });

                const result = await response.json();

                if (result.success) {
                    window.Toast?.success(result.message);
                    document.getElementById('edit-weight-modal').close();
                    // Reload the bookings for the selected date
                    if (selectedDate) {
                        loadBookingsByDate(selectedDate);
                    }
                } else {
                    window.Toast?.error(result.message || 'Failed to update weight');
                }
            } catch (error) {
                console.error('Error:', error);
                window.Toast?.error('Failed to update weight');
            }
        });
    }

    window.cancelBooking = async (id) => {
        if (confirm('Are you sure you want to cancel this booking?')) {
            try {
                const response = await fetch(`${routes.bookings}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.Toast?.success('Booking cancelled successfully');
                    // Reload the date bookings
                    const dateInput = document.getElementById('booking_date');
                    if (dateInput && dateInput.value) {
                        await loadDateBookings(dateInput.value);
                    }
                    // Refresh calendar
                    calendar.render();
                } else {
                    window.Toast?.error('Failed to cancel booking: ' + data.message);
                }
            } catch (error) {
                console.error('Failed to cancel booking:', error);
                window.Toast?.error('Failed to cancel booking');
            }
        }
    };

    // Initialize Booking Form
    const bookingForm = new BookingForm({
        servicesData: services,
        productsData: products
    });
    bookingForm.init();

    // Initialize User Search
    const userSearch = new UserSearch({
        inputId: 'user-search',
        resultsId: 'user-search-results',
        searchUrl: routes.userSearch,
        onUserSelect: (user) => {
            // Fill in user details
            const userIdInput = document.getElementById('user_id');
            const pickupAddress = document.getElementById('pickup_address');
            const latitude = document.getElementById('latitude');
            const longitude = document.getElementById('longitude');
            
            if (userIdInput) userIdInput.value = user.id;
            if (pickupAddress) pickupAddress.value = user.address;
            if (latitude) latitude.value = user.latitude || '';
            if (longitude) longitude.value = user.longitude || '';
            
            // Show user info
            const selectedUserInfo = document.getElementById('selected-user-info');
            if (selectedUserInfo) {
                document.getElementById('selected-user-name').textContent = user.name;
                document.getElementById('selected-user-email').textContent = user.email;
                document.getElementById('selected-user-phone').textContent = user.phone;
                document.getElementById('selected-user-address').textContent = user.address;
                selectedUserInfo.classList.remove('hidden');
            }
            
            // Load user bookings
            bookingManagement.loadUserBookings(user.id);
            
            // Update bookings user name
            const nameEl = document.getElementById('bookings-user-name');
            if (nameEl) {
                nameEl.textContent = user.name;
            }
        }
    });
    userSearch.init();

    // Clear user button
    const clearUserBtn = document.getElementById('clear-user');
    if (clearUserBtn) {
        clearUserBtn.addEventListener('click', () => {
            const userIdInput = document.getElementById('user_id');
            const selectedUserInfo = document.getElementById('selected-user-info');
            const userBookingsSection = document.getElementById('user-bookings-section');
            
            if (userIdInput) userIdInput.value = '';
            if (selectedUserInfo) selectedUserInfo.classList.add('hidden');
            if (userBookingsSection) userBookingsSection.classList.add('hidden');
        });
    }

    // Service type toggle (pickup vs dropoff)
    const serviceTypeRadios = document.querySelectorAll('input[name="service_type"]');
    const pickupAddressSection = document.getElementById('pickup-address-section');
    const pickupAddressInput = document.getElementById('pickup_address');

    function updateServiceTypeUI() {
        const selectedType = document.querySelector('input[name="service_type"]:checked')?.value;
        
        if (pickupAddressSection) {
            if (selectedType === 'dropoff') {
                pickupAddressSection.classList.add('hidden');
                if (pickupAddressInput) pickupAddressInput.removeAttribute('required');
            } else {
                pickupAddressSection.classList.remove('hidden');
                if (pickupAddressInput) pickupAddressInput.setAttribute('required', 'required');
            }
        }
    }

    serviceTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateServiceTypeUI);
    });

    // Initialize service type UI
    updateServiceTypeUI();

    // Booking type toggle (online vs walkin)
    const bookingTypeRadios = document.querySelectorAll('input[name="booking_type"]');
    const customerSelectionSection = document.getElementById('customer-selection-section');
    const userIdInput = document.getElementById('user_id');

    function updateBookingTypeUI() {
        const selectedType = document.querySelector('input[name="booking_type"]:checked')?.value;
        
        if (customerSelectionSection && userIdInput) {
            if (selectedType === 'walkin') {
                // Hide customer selection for walk-in
                customerSelectionSection.classList.add('hidden');
                userIdInput.removeAttribute('required');
                userIdInput.value = ''; // Clear user selection
                
                // Hide pickup address section for walk-in (they're already at the branch)
                if (pickupAddressSection) {
                    pickupAddressSection.classList.add('hidden');
                    if (pickupAddressInput) pickupAddressInput.removeAttribute('required');
                }
                
                // Force dropoff service type for walk-in
                const dropoffRadio = document.querySelector('input[name="service_type"][value="dropoff"]');
                if (dropoffRadio) {
                    dropoffRadio.checked = true;
                    dropoffRadio.closest('.bg-white').classList.add('hidden'); // Hide service type selection
                }
            } else {
                // Show customer selection for online booking
                customerSelectionSection.classList.remove('hidden');
                userIdInput.setAttribute('required', 'required');
                
                // Show service type selection
                const serviceTypeSection = document.querySelector('input[name="service_type"]')?.closest('.bg-white');
                if (serviceTypeSection) {
                    serviceTypeSection.classList.remove('hidden');
                }
                
                // Update service type UI based on selection
                updateServiceTypeUI();
            }
        }
    }

    bookingTypeRadios.forEach(radio => {
        radio.addEventListener('change', updateBookingTypeUI);
    });

    // Initialize booking type UI
    updateBookingTypeUI();

    // Clear form button
    const clearBtn = document.getElementById('clear-form');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            const form = document.getElementById('booking-form');
            if (form) form.reset();
            bookingForm.reset();
            
            const dateInput = document.getElementById('booking_date');
            const currentDate = dateInput ? dateInput.value : null;
            if (dateInput) dateInput.value = '';
            timeSlots.clear();
            
            // Hide form
            const bookingFormEl = document.getElementById('booking-form');
            const dateBookingsSection = document.getElementById('date-bookings-section');
            const emptyState = document.getElementById('empty-state');
            const userBookingsSection = document.getElementById('user-bookings-section');
            
            if (bookingFormEl) bookingFormEl.classList.add('hidden');
            
            // If a date was selected, show the date bookings section again
            if (currentDate && dateBookingsSection) {
                dateBookingsSection.classList.remove('hidden');
                dateBookingsSection.style.opacity = '1';
            } else {
                // Otherwise show empty state
                if (dateBookingsSection) dateBookingsSection.classList.add('hidden');
                if (emptyState) {
                    emptyState.classList.remove('hidden');
                    emptyState.style.opacity = '1';
                }
            }
            
            if (userBookingsSection) userBookingsSection.classList.add('hidden');
            
            // Clear user selection
            const userIdInput = document.getElementById('user_id');
            const selectedUserInfo = document.getElementById('selected-user-info');
            if (userIdInput) userIdInput.value = '';
            if (selectedUserInfo) selectedUserInfo.classList.add('hidden');
            
            // Reset calendar selection if no date
            if (!currentDate) {
                calendar.reset();
            }
        });
    }
});


// Prevent double form submission
let isSubmitting = false;
window.handleBookingSubmit = function(event) {
    if (isSubmitting) {
        event.preventDefault();
        return false;
    }
    
    isSubmitting = true;
    
    // Disable submit button
    const submitBtn = event.target.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Submitting...';
    }
    
    // Re-enable after 3 seconds as a safety measure
    setTimeout(() => {
        isSubmitting = false;
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Booking';
        }
    }, 3000);
    
    return true;
};
