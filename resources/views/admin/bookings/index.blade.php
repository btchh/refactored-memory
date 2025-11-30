<x-layout>
    <x-slot name="title">Booking Management</x-slot>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-start sm:items-center gap-3">
                <svg class="w-8 h-8 text-primary-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Booking Management</h1>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">Create and manage customer bookings</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error mb-4">{{ session('error') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Left Column: Calendar (40%) -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Select Date
                        </h2>
                    </div>
                    <div class="card-body">
                        <!-- Calendar will be rendered here by JavaScript -->
                        <div id="calendar-container" class="mb-4">
                            <div class="flex justify-between items-center mb-4">
                                <button id="prev-month" class="btn btn-sm btn-icon" aria-label="Previous month">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <h3 id="current-month" class="text-lg font-semibold"></h3>
                                <button id="next-month" class="btn btn-sm btn-icon" aria-label="Next month">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                            <div id="calendar-grid" class="grid grid-cols-7 gap-2"></div>
                        </div>

                        <!-- Legend -->
                        <div class="flex gap-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <span>Available</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                                <span>Past</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Form (60%) -->
            <div class="lg:col-span-3">
                <!-- Date Bookings List (shown when date is selected) -->
                <div id="date-bookings-section" class="card mb-6 hidden animate-fade-in">
                    <div class="card-header bg-gradient-to-r from-primary-50 to-primary-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h2 class="card-title flex items-center gap-2 mb-1">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span id="selected-date-display" class="text-primary-900 font-bold"></span>
                                </h2>
                                <p class="text-sm text-primary-700 flex items-center gap-1">
                                    <span id="bookings-count-badge" class="font-semibold">0</span> booking(s) scheduled
                                </p>
                            </div>
                            <button type="button" id="show-add-form-btn" class="btn btn-primary btn-sm shadow-md hover:shadow-lg transition-all">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add New Booking
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="date-bookings-list" class="space-y-3"></div>
                    </div>
                </div>

                <!-- Empty State (shown when no date selected) -->
                <div id="empty-state" class="card border-2 border-dashed border-gray-300 bg-gray-50">
                    <div class="card-body text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-100 rounded-full mb-4 animate-pulse">
                            <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Select a Date to Begin</h3>
                        <p class="text-gray-600 max-w-md mx-auto">Choose a date from the calendar to view existing bookings or create new ones</p>
                        <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                            <span>Click any available date on the left</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.bookings.store') }}" method="POST" id="booking-form" class="space-y-6 hidden" onsubmit="if(this.dataset.submitting==='true')return false;this.dataset.submitting='true';this.querySelector('[type=submit]').disabled=true;">
                    @csrf

                    <!-- User Selection -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Select Customer
                            </h2>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Search User</label>
                                <div class="relative">
                                    <input type="text" id="user-search" placeholder="Search by name or email..." 
                                        class="form-input" autocomplete="off">
                                    <div id="user-search-results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                                </div>
                            </div>

                            <input type="hidden" name="user_id" id="user_id" required>

                            <!-- Selected User Info -->
                            <div id="selected-user-info" class="mt-4 p-4 bg-primary-50 border border-primary-200 rounded-lg hidden">
                                <p class="font-semibold text-primary-900" id="selected-user-name"></p>
                                <p class="text-sm text-primary-700" id="selected-user-email"></p>
                                <p class="text-sm text-primary-700" id="selected-user-phone"></p>
                                <p class="text-sm text-primary-700" id="selected-user-address"></p>
                                <button type="button" id="clear-user" class="btn btn-sm btn-outline mt-2">Clear Selection</button>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Booking Details
                            </h2>
                        </div>
                        <div class="card-body">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Date -->
                                <div class="form-group">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="booking_date" id="booking_date" class="form-input" required readonly>
                                </div>

                                <!-- Time -->
                                <div class="form-group">
                                    <label class="form-label">Time</label>
                                    <select name="booking_time" id="booking_time" class="form-select" required>
                                        <option value="">Select time slot</option>
                                    </select>
                                </div>

                                <!-- Item Type -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Item Type</label>
                                    <select name="item_type" id="item_type" class="form-select" required>
                                        <option value="">Select item type</option>
                                        <option value="clothes">Clothes</option>
                                        <option value="comforter">Comforter</option>
                                        <option value="shoes">Shoes</option>
                                    </select>
                                </div>

                                <!-- Services -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Services</label>
                                    <div id="services-container" class="grid grid-cols-2 gap-2">
                                        <p class="text-gray-500 col-span-2">Select an item type first</p>
                                    </div>
                                </div>

                                <!-- Products -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Products</label>
                                    <div id="products-container" class="grid grid-cols-2 gap-2">
                                        <p class="text-gray-500 col-span-2">Select an item type first</p>
                                    </div>
                                </div>

                                <!-- Pickup Address -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Pickup Address</label>
                                    <input type="text" name="pickup_address" id="pickup_address" class="form-input" required readonly>
                                    <input type="hidden" name="latitude" id="latitude">
                                    <input type="hidden" name="longitude" id="longitude">
                                </div>

                                <!-- Notes -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" class="form-textarea" rows="3" placeholder="Special instructions..."></textarea>
                                </div>

                                <!-- Weight -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Weight (kg) - Optional</label>
                                    <input type="number" name="weight" id="weight" class="form-input" step="0.01" placeholder="For documentation">
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div class="mt-6 p-4 bg-success-50 border-l-4 border-success-500 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-success-900">Total Price:</span>
                                    <span class="text-2xl font-bold text-success-600">₱<span id="total-price">0.00</span></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4 mt-6">
                                <button type="button" id="clear-form" class="btn btn-secondary flex-1">Clear Form</button>
                                <button type="submit" class="btn btn-primary flex-1">Create Booking</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- User's Bookings List -->
                <div id="user-bookings-section" class="card mt-6 hidden">
                    <div class="card-header">
                        <h2 class="card-title flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span id="bookings-user-name"></span>'s Bookings
                        </h2>
                    </div>
                    <div class="card-body">
                        <div id="user-bookings-list" class="space-y-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Weight Modal -->
    <dialog id="edit-weight-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Update Weight</h3>
            
            <form id="edit-weight-form" class="space-y-4">
                <input type="hidden" id="edit-weight-booking-id">
                <div class="form-group">
                    <label class="form-label" for="edit-booking-weight">Weight (kg)</label>
                    <input type="number" id="edit-booking-weight" class="form-input" step="0.01" min="0" required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-weight-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Weight</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- View Booking Modal -->
    <dialog id="view-booking-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <form method="dialog" class="absolute top-4 right-4">
                <button type="submit" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-6">Booking Details</h3>
            
            <div id="view-booking-content" class="space-y-4">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </dialog>

    <!-- Reschedule Modal -->
    <dialog id="reschedule-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-lg">
            <div class="modal-header border-b border-gray-200 pb-4 mb-4 flex items-center justify-between">
                <h3 class="modal-title text-xl font-semibold text-gray-900">Reschedule Booking</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 rounded transition-colors duration-200" onclick="reschedule_modal.close()" aria-label="Close modal">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="reschedule-form">
                <input type="hidden" id="reschedule-booking-id">
                <div class="form-group">
                    <label class="form-label" for="reschedule-date">
                        New Date
                    </label>
                    <input type="date" id="reschedule-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="reschedule-time">
                        New Time
                    </label>
                    <select id="reschedule-time" class="form-select" required>
                        <option value="">Select time</option>
                    </select>
                </div>
                <div class="modal-footer border-t border-gray-200 pt-4 mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-outline" onclick="reschedule_modal.close()">Cancel</button>
                    <button type="submit" class="btn-primary">Reschedule</button>
                </div>
            </form>
        </div>
    </dialog>

    @push('scripts')
    <script>
        // Pass data to JavaScript module
        window.bookingData = {
            services: @json($services),
            products: @json($products),
            routes: {
                slots: '{{ route('admin.api.calendar.slots') }}',
                userSearch: '{{ route('admin.api.users.search') }}',
                userBookings: '/admin/api/bookings/user',
                bookingsByDate: '{{ route('admin.api.bookings.by-date') }}',
                bookingCounts: '{{ route('admin.api.bookings.counts') }}',
                bookings: '/admin/bookings',
                calculate: '{{ route('admin.api.bookings.calculate') }}',
                bookingDetails: '{{ route('admin.bookings.details', ['id' => '__ID__']) }}',
                updateWeight: '{{ route('admin.bookings.updateWeight', ['id' => '__ID__']) }}'
            },
            csrf: '{{ csrf_token() }}'
        };
    </script>
    @vite(['resources/js/pages/admin-booking.js'])
    @endpush
</x-layout>
