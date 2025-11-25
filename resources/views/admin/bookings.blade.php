<x-layout>
    <x-slot:title>Manage Bookings</x-slot:title>
    <x-nav type="admin" />
    
    <div class="min-h-screen bg-gray-100">
        <main class="container mx-auto p-4">
            <!-- Page Header -->
            <div class="flex justify-between items-center my-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage All Bookings</h1>
            </div>

            <!-- Search and Filter Controls -->
            <x-modules.card class="mb-4">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search Input -->
                        <div class="md:col-span-2">
                            <x-modules.input 
                                type="text" 
                                id="searchInput"
                                name="search" 
                                label="Search" 
                                placeholder="Search by title, attendee, or location"
                            />
                        </div>

                        <!-- User Filter -->
                        <div>
                            <x-modules.select 
                                id="userFilter"
                                name="user_filter" 
                                label="Filter by User"
                            >
                                <option value="">All Users</option>
                            </x-modules.select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <x-modules.select 
                                id="statusFilter"
                                name="status_filter" 
                                label="Filter by Status"
                            >
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="rescheduled">Rescheduled</option>
                            </x-modules.select>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-modules.input 
                            type="date" 
                            id="startDateFilter"
                            name="start_date" 
                            label="Start Date"
                        />

                        <x-modules.input 
                            type="date" 
                            id="endDateFilter"
                            name="end_date" 
                            label="End Date"
                        />

                        <div class="flex items-end space-x-2">
                            <x-modules.button 
                                type="button" 
                                variant="primary" 
                                onclick="applyFilters()"
                                class="flex-1"
                            >
                                Apply Filters
                            </x-modules.button>
                            <x-modules.button 
                                type="button" 
                                variant="secondary" 
                                onclick="clearFilters()"
                                class="flex-1"
                            >
                                Clear Filters
                            </x-modules.button>
                        </div>
                    </div>
                </div>
            </x-modules.card>

            <!-- Bookings List -->
            <x-modules.card>
                <div id="bookingsContainer">
                    <div class="text-center py-8">
                        <p class="text-gray-500">Loading bookings...</p>
                    </div>
                </div>
            </x-modules.card>
        </main>
    </div>

    <!-- Edit Booking Modal -->
    <x-modules.modal id="editBookingModal" title="Edit Booking" size="lg">
        <form id="editBookingForm">
            @csrf
            <input type="hidden" id="edit_booking_id" name="booking_id">
            
            <!-- User Information (Read-only) -->
            <div class="mb-4 p-3 bg-gray-50 rounded">
                <p class="text-sm text-gray-600"><strong>User:</strong> <span id="edit_user_info"></span></p>
            </div>
            
            <div class="space-y-4">
                <x-modules.input 
                    type="text" 
                    name="title" 
                    id="edit_title"
                    label="Title" 
                    placeholder="Enter booking title"
                    required
                />

                <x-modules.textarea 
                    name="description" 
                    id="edit_description"
                    label="Description" 
                    placeholder="Enter booking description"
                    rows="3"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="datetime-local" 
                        name="start_time" 
                        id="edit_start_time"
                        label="Start Time" 
                        required
                    />

                    <x-modules.input 
                        type="datetime-local" 
                        name="end_time" 
                        id="edit_end_time"
                        label="End Time" 
                        required
                    />
                </div>

                <x-modules.input 
                    type="text" 
                    name="location" 
                    id="edit_location"
                    label="Location" 
                    placeholder="Enter location"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="text" 
                        name="attendee_name" 
                        id="edit_attendee_name"
                        label="Attendee Name" 
                        placeholder="Enter attendee name"
                    />

                    <x-modules.input 
                        type="email" 
                        name="attendee_email" 
                        id="edit_attendee_email"
                        label="Attendee Email" 
                        placeholder="Enter attendee email"
                    />
                </div>

                <x-modules.input 
                    type="tel" 
                    name="attendee_phone" 
                    id="edit_attendee_phone"
                    label="Attendee Phone" 
                    placeholder="Enter attendee phone"
                />

                <x-modules.select 
                    id="edit_status"
                    name="status" 
                    label="Status"
                    required
                >
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="rescheduled">Rescheduled</option>
                </x-modules.select>

                <x-modules.textarea 
                    name="notes" 
                    id="edit_notes"
                    label="Notes" 
                    placeholder="Additional notes"
                    rows="3"
                />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-modules.button type="button" variant="secondary" onclick="hideModal('editBookingModal')">
                    Cancel
                </x-modules.button>
                <x-modules.button type="submit" variant="primary">
                    Update Booking
                </x-modules.button>
            </div>
        </form>
    </x-modules.modal>

    <!-- Cancel Booking Modal -->
    <x-modules.modal id="cancelBookingModal" title="Cancel Booking" size="md">
        <div>
            <p class="text-gray-700 mb-4">Are you sure you want to cancel this booking?</p>
            <p class="text-sm text-gray-600 mb-2"><strong>Title:</strong> <span id="cancel_booking_title"></span></p>
            <p class="text-sm text-gray-600 mb-2"><strong>User:</strong> <span id="cancel_booking_user"></span></p>
            <p class="text-sm text-gray-600 mb-4"><strong>Date:</strong> <span id="cancel_booking_date"></span></p>
            
            <input type="hidden" id="cancel_booking_id">
            
            <x-modules.textarea 
                id="cancel_reason"
                name="reason" 
                label="Cancellation Reason (Optional)" 
                placeholder="Enter reason for cancellation"
                rows="3"
            />
            
            <div class="mt-6 flex justify-end space-x-3">
                <x-modules.button type="button" variant="secondary" onclick="hideModal('cancelBookingModal')">
                    No, Keep It
                </x-modules.button>
                <x-modules.button type="button" variant="danger" onclick="confirmCancelBooking()">
                    Yes, Cancel Booking
                </x-modules.button>
            </div>
        </div>
    </x-modules.modal>

    <x-notifications />

    <script>
        let bookings = [];
        let allUsers = [];
        let filteredBookings = [];

        // Load bookings and users on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadBookings();
            loadUsers();
        });

        // Load all bookings from server
        function loadBookings() {
            const params = new URLSearchParams();
            
            // Add filter parameters if they exist
            const userFilter = document.getElementById('userFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const startDate = document.getElementById('startDateFilter').value;
            const endDate = document.getElementById('endDateFilter').value;
            const search = document.getElementById('searchInput').value;
            
            if (userFilter) params.append('user_id', userFilter);
            if (statusFilter) params.append('status', statusFilter);
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (search) params.append('search', search);
            
            const url = '{{ route('admin.bookings.data') }}' + (params.toString() ? '?' + params.toString() : '');
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bookings = data.bookings;
                    filteredBookings = bookings;
                    renderBookings();
                } else {
                    showNotification('error', data.message || 'Failed to load bookings');
                }
            })
            .catch(error => {
                console.error('Error loading bookings:', error);
                showNotification('error', 'Failed to load bookings');
            });
        }

        // Load users for filter dropdown
        function loadUsers() {
            // This would typically fetch from an API endpoint
            // For now, we'll populate it from the bookings data
            // In a real implementation, you'd have a separate endpoint for users
        }

        // Populate user filter dropdown from bookings
        function populateUserFilter() {
            const userFilter = document.getElementById('userFilter');
            const uniqueUsers = new Map();
            
            bookings.forEach(booking => {
                if (booking.user && !uniqueUsers.has(booking.user.id)) {
                    uniqueUsers.set(booking.user.id, booking.user);
                }
            });
            
            // Clear existing options except "All Users"
            while (userFilter.options.length > 1) {
                userFilter.remove(1);
            }
            
            // Add user options
            uniqueUsers.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.username} (${user.email})`;
                userFilter.appendChild(option);
            });
        }

        // Apply filters
        function applyFilters() {
            loadBookings();
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('userFilter').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('startDateFilter').value = '';
            document.getElementById('endDateFilter').value = '';
            loadBookings();
        }

        // Render bookings list
        function renderBookings() {
            const container = document.getElementById('bookingsContainer');
            
            // Populate user filter if not already done
            populateUserFilter();
            
            if (bookings.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-gray-500">No bookings found.</p>
                    </div>
                `;
                return;
            }

            const bookingsHtml = bookings.map(booking => {
                const startDate = new Date(booking.start_time);
                const endDate = new Date(booking.end_time);
                const duration = Math.round((endDate - startDate) / (1000 * 60)); // minutes
                
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'confirmed': 'bg-green-100 text-green-800',
                    'cancelled': 'bg-red-100 text-red-800',
                    'rescheduled': 'bg-blue-100 text-blue-800'
                };
                
                const statusClass = statusColors[booking.status] || 'bg-gray-100 text-gray-800';
                
                // User information
                const userName = booking.user ? booking.user.username : 'Unknown User';
                const userEmail = booking.user ? booking.user.email : '';
                
                return `
                    <div class="border-b border-gray-200 py-4 hover:bg-gray-50 transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">${escapeHtml(booking.title)}</h3>
                                    <span class="px-2 py-1 text-xs font-medium rounded ${statusClass}">
                                        ${booking.status.toUpperCase()}
                                    </span>
                                </div>
                                
                                <!-- User Information -->
                                <div class="mb-2 p-2 bg-blue-50 rounded inline-block">
                                    <span class="text-sm text-blue-800">
                                        <strong>User:</strong> ${escapeHtml(userName)}
                                        ${userEmail ? `(${escapeHtml(userEmail)})` : ''}
                                    </span>
                                </div>
                                
                                ${booking.description ? `<p class="text-gray-600 text-sm mb-2">${escapeHtml(booking.description)}</p>` : ''}
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div>
                                        <strong>Date:</strong> ${formatDate(startDate)}
                                    </div>
                                    <div>
                                        <strong>Time:</strong> ${formatTime(startDate)} - ${formatTime(endDate)}
                                    </div>
                                    <div>
                                        <strong>Duration:</strong> ${duration} minutes
                                    </div>
                                    ${booking.location ? `<div><strong>Location:</strong> ${escapeHtml(booking.location)}</div>` : ''}
                                </div>
                                
                                ${booking.attendee_name || booking.attendee_email || booking.attendee_phone ? `
                                    <div class="mt-2 text-sm text-gray-600">
                                        <strong>Attendee:</strong>
                                        ${booking.attendee_name ? escapeHtml(booking.attendee_name) : ''}
                                        ${booking.attendee_email ? `(${escapeHtml(booking.attendee_email)})` : ''}
                                        ${booking.attendee_phone ? ` - ${escapeHtml(booking.attendee_phone)}` : ''}
                                    </div>
                                ` : ''}
                                
                                ${booking.notes ? `<div class="mt-2 text-sm text-gray-500"><strong>Notes:</strong> ${escapeHtml(booking.notes)}</div>` : ''}
                            </div>
                            
                            <div class="flex space-x-2 ml-4">
                                <button onclick="editBooking(${booking.id})" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    Edit
                                </button>
                                ${booking.status !== 'cancelled' ? `
                                    <button onclick="showCancelModal(${booking.id})" class="px-3 py-1 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition">
                                        Cancel
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = bookingsHtml;
        }

        // Edit booking
        function editBooking(bookingId) {
            const booking = bookings.find(b => b.id === bookingId);
            if (!booking) return;
            
            document.getElementById('edit_booking_id').value = booking.id;
            
            // Display user information
            const userName = booking.user ? booking.user.username : 'Unknown User';
            const userEmail = booking.user ? booking.user.email : '';
            document.getElementById('edit_user_info').textContent = `${userName}${userEmail ? ` (${userEmail})` : ''}`;
            
            // Populate form fields
            document.getElementById('edit_title').value = booking.title;
            document.getElementById('edit_description').value = booking.description || '';
            document.getElementById('edit_start_time').value = formatDateTimeLocal(booking.start_time);
            document.getElementById('edit_end_time').value = formatDateTimeLocal(booking.end_time);
            document.getElementById('edit_location').value = booking.location || '';
            document.getElementById('edit_attendee_name').value = booking.attendee_name || '';
            document.getElementById('edit_attendee_email').value = booking.attendee_email || '';
            document.getElementById('edit_attendee_phone').value = booking.attendee_phone || '';
            document.getElementById('edit_status').value = booking.status;
            document.getElementById('edit_notes').value = booking.notes || '';
            
            showModal('editBookingModal');
        }

        // Update booking form submission
        document.getElementById('editBookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const bookingId = document.getElementById('edit_booking_id').value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            delete data.booking_id;
            
            fetch(`/admin/bookings/${bookingId}/manage`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message || 'Booking updated successfully');
                    hideModal('editBookingModal');
                    loadBookings();
                } else {
                    showNotification('error', data.message || 'Failed to update booking');
                }
            })
            .catch(error => {
                console.error('Error updating booking:', error);
                showNotification('error', 'Failed to update booking');
            });
        });

        // Show cancel modal
        function showCancelModal(bookingId) {
            const booking = bookings.find(b => b.id === bookingId);
            if (!booking) return;
            
            const userName = booking.user ? booking.user.username : 'Unknown User';
            
            document.getElementById('cancel_booking_id').value = booking.id;
            document.getElementById('cancel_booking_title').textContent = booking.title;
            document.getElementById('cancel_booking_user').textContent = userName;
            document.getElementById('cancel_booking_date').textContent = formatDate(new Date(booking.start_time));
            document.getElementById('cancel_reason').value = '';
            
            showModal('cancelBookingModal');
        }

        // Confirm cancel booking
        function confirmCancelBooking() {
            const bookingId = document.getElementById('cancel_booking_id').value;
            const reason = document.getElementById('cancel_reason').value;
            
            fetch(`/admin/bookings/${bookingId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('success', data.message || 'Booking cancelled successfully');
                    hideModal('cancelBookingModal');
                    loadBookings();
                } else {
                    showNotification('error', data.message || 'Failed to cancel booking');
                }
            })
            .catch(error => {
                console.error('Error cancelling booking:', error);
                showNotification('error', 'Failed to cancel booking');
            });
        }

        // Utility functions
        function formatDate(date) {
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }

        function formatTime(date) {
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        }

        function formatDateTimeLocal(dateString) {
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showNotification(type, message) {
            // Use existing notification system
            if (window.showNotification) {
                window.showNotification(type, message);
            } else {
                alert(message);
            }
        }
    </script>
</x-layout>
