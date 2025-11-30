<x-layout>
    <x-slot name="title">Book Laundry Service</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.card class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Book Laundry Service</h1>
            <p class="text-gray-600">Select a date and time to schedule your laundry pickup</p>
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

            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <!-- Empty State -->
                <div id="empty-state" class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Select a Date</h3>
                    <p class="text-gray-500">Choose a date from the calendar to start booking</p>
                </div>

                <!-- Booking Form (Hidden by default) -->
                <form action="{{ route('user.booking.submit') }}" method="POST" id="booking-form" class="hidden space-y-6">
                    @csrf

                    <!-- Your Information -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Your Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" value="{{ $user->fname }} {{ $user->lname }}" class="form-input bg-gray-50" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="text" value="{{ $user->email }}" class="form-input bg-gray-50" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" value="{{ $user->phone }}" class="form-input bg-gray-50" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" value="{{ $user->address }}" class="form-input bg-gray-50" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Selection -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-4">Select Branch</h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span class="text-red-500">*</span></label>
                            <select name="admin_id" id="admin_id" class="form-select" required>
                                <option value="">Choose your preferred branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch['id'] }}" data-address="{{ $branch['address'] }}" data-phone="{{ $branch['phone'] }}">
                                        {{ $branch['name'] }} - {{ $branch['branch_name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <div id="branch-info" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                <p class="text-sm text-blue-700" id="branch-address"></p>
                                <p class="text-sm text-blue-700" id="branch-phone"></p>
                            </div>
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
                                <label class="block text-sm font-medium text-gray-700 mb-1">Time <span class="text-red-500">*</span></label>
                                <select name="booking_time" id="booking_time" class="form-select" required>
                                    <option value="">Select time slot</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Item Type <span class="text-red-500">*</span></label>
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
                                <input type="text" name="pickup_address" value="{{ $user->address }}" class="form-input bg-gray-50" required readonly>
                                <input type="hidden" name="latitude" value="{{ $user->latitude }}">
                                <input type="hidden" name="longitude" value="{{ $user->longitude }}">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                                <textarea name="notes" id="notes" class="form-textarea" rows="2" placeholder="Any special requests..."></textarea>
                            </div>
                        </div>

                        <!-- Total & Actions -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-lg font-semibold text-gray-900">Total:</span>
                                <span class="text-2xl font-bold text-green-600">₱<span id="total-price">0.00</span></span>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" id="clear-selection" class="btn btn-outline flex-1">Clear</button>
                                <button type="submit" class="btn btn-primary flex-1">Submit Booking</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.bookingData = {
            services: @json($services),
            products: @json($products),
            routes: {
                slots: '{{ route('user.api.calendar.slots') }}',
                calculate: '{{ route('user.api.bookings.calculate') }}'
            }
        };
    </script>
    @vite(['resources/js/pages/user-booking.js'])
    @endpush
</x-layout>
