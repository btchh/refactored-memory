<x-layout>
    <x-slot name="title">Booking Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.card class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Booking Management</h1>
            <p class="text-gray-600">Create and manage customer bookings</p>
        </x-modules.card>

        <!-- Alerts -->
        @if(session('success'))
            <x-modules.alert type="success">{{ session('success') }}</x-modules.alert>
        @endif
        @if(session('error'))
            <x-modules.alert type="error">{{ session('error') }}</x-modules.alert>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Select Date</h2>
                    
                    <div id="calendar-container" class="mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <button id="prev-month" class="p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <h3 id="current-month" class="text-lg font-semibold text-gray-800"></h3>
                            <button id="next-month" class="p-2 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                        <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>
                    </div>

                    <div class="flex gap-4 text-sm pt-4 border-t border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-gray-600">Available</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                            <span class="text-gray-600">Past</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Date Bookings Section -->
                <div id="date-bookings-section" class="bg-white rounded-lg border border-gray-200 hidden">
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-gray-900" id="selected-date-display"></h2>
                            <p class="text-sm text-gray-500"><span id="bookings-count-badge">0</span> booking(s)</p>
                        </div>
                        <button type="button" id="show-add-form-btn" class="btn btn-primary btn-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Booking
                        </button>
                    </div>
                    <div class="p-4">
                        <div id="date-bookings-list" class="space-y-3"></div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Select a Date</h3>
                    <p class="text-gray-500">Choose a date from the calendar to view or create bookings</p>
                </div>


                <!-- Booking Form -->
                <form action="{{ route('admin.bookings.store') }}" method="POST" id="booking-form" class="hidden space-y-6">
                    @csrf

                    <!-- Customer Selection -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Select Customer</h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search Customer</label>
                            <input type="text" id="user-search" placeholder="Search by name or email..." class="form-input" autocomplete="off">
                            <div id="user-search-results" class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 hidden max-h-60 overflow-y-auto"></div>
                        </div>
                        <input type="hidden" name="user_id" id="user_id" required>
                        
                        <div id="selected-user-info" class="mt-4 p-4 bg-primary-50 border border-primary-200 rounded-lg hidden">
                            <p class="font-semibold text-primary-900" id="selected-user-name"></p>
                            <p class="text-sm text-primary-700" id="selected-user-email"></p>
                            <p class="text-sm text-primary-700" id="selected-user-phone"></p>
                            <p class="text-sm text-primary-700" id="selected-user-address"></p>
                            <button type="button" id="clear-user" class="btn btn-sm btn-outline mt-2">Clear</button>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Booking Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="booking_date" id="booking_date" class="form-input bg-gray-50" required readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                                <select name="booking_time" id="booking_time" class="form-select" required>
                                    <option value="">Select time slot</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Type</label>
                                <select name="item_type" id="item_type" class="form-select" required>
                                    <option value="">Select item type</option>
                                    <option value="clothes">Clothes</option>
                                    <option value="comforter">Comforter</option>
                                    <option value="shoes">Shoes</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Services</label>
                                <div id="services-container" class="grid grid-cols-2 gap-2">
                                    <p class="text-gray-500 col-span-2 text-sm">Select an item type first</p>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Products</label>
                                <div id="products-container" class="grid grid-cols-2 gap-2">
                                    <p class="text-gray-500 col-span-2 text-sm">Select an item type first</p>
                                </div>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Address</label>
                                <input type="text" name="pickup_address" id="pickup_address" class="form-input bg-gray-50" required readonly>
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                <textarea name="notes" id="notes" class="form-textarea" rows="2" placeholder="Special instructions..."></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg) - Optional</label>
                                <input type="number" name="weight" id="weight" class="form-input" step="0.01" placeholder="For documentation">
                            </div>
                        </div>

                        <!-- Total & Actions -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span class="text-2xl font-bold text-green-600">₱<span id="total-price">0.00</span></span>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" id="clear-form" class="btn btn-outline flex-1">Clear</button>
                                <button type="submit" class="btn btn-primary flex-1">Create Booking</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- User's Bookings List -->
                <div id="user-bookings-section" class="bg-white rounded-lg border border-gray-200 hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="font-bold text-gray-900"><span id="bookings-user-name"></span>'s Bookings</h2>
                    </div>
                    <div class="p-4">
                        <div id="user-bookings-list" class="space-y-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Weight Modal -->
    <dialog id="edit-weight-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Update Weight</h3>
            <form id="edit-weight-form">
                <input type="hidden" id="edit-weight-booking-id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                    <input type="number" id="edit-booking-weight" class="form-input" step="0.01" min="0" required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-weight-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- View Booking Modal -->
    <dialog id="view-booking-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Details</h3>
            <div id="view-booking-content"></div>
            <div class="mt-4 flex justify-end">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('view-booking-modal').close()">Close</button>
            </div>
        </div>
    </dialog>

    <!-- Reschedule Modal -->
    <dialog id="reschedule-modal" class="modal">
        <div class="modal-box bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Reschedule Booking</h3>
            <form id="reschedule-form">
                <input type="hidden" id="reschedule-booking-id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Date</label>
                    <input type="date" id="reschedule-date" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Time</label>
                    <select id="reschedule-time" class="form-select" required>
                        <option value="">Select time</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('reschedule-modal').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Reschedule</button>
                </div>
            </form>
        </div>
    </dialog>

    @push('scripts')
    <script>
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
