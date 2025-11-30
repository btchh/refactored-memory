<x-layout>
    <x-slot name="title">Book Laundry Service</x-slot>

    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Clean Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                    Book Your Laundry
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Select a date from the calendar and let us handle the rest
                </p>
            </div>

            <!-- Success/Error Messages with Modern Design -->
            @if(session('success'))
                <div class="alert alert-success mb-6 shadow-lg border-l-4 border-green-500 animate-slide-down">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-error mb-6 shadow-lg border-l-4 border-red-500 animate-slide-down">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <!-- Left Column: Calendar -->
                <div class="lg:col-span-2">
                    <div class="card shadow-md hover:shadow-lg transition-shadow duration-200">
                        <div class="card-header bg-gradient-to-r from-primary-50 to-primary-100">
                            <h2 class="card-title flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-primary-900 font-bold">Select Date</span>
                            </h2>
                        </div>
                        <div class="card-body">
                            
                            <!-- Calendar -->
                            <div id="calendar-container" class="mb-6">
                                <div class="flex justify-between items-center mb-6 bg-gray-50 p-4 rounded-lg">
                                    <button id="prev-month" class="btn btn-icon" aria-label="Previous month">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                    </button>
                                    <h3 id="current-month" class="text-xl font-semibold text-gray-800"></h3>
                                    <button id="next-month" class="btn btn-icon" aria-label="Next month">
                                        <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                    </button>
                                </div>
                                <div id="calendar-grid" class="grid grid-cols-7 gap-2"></div>
                            </div>

                            <!-- Legend -->
                            <div class="flex gap-6 text-sm bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full bg-success"></div>
                                    <span class="font-medium text-gray-700">Available</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-4 h-4 rounded-full bg-gray-300"></div>
                                    <span class="font-medium text-gray-700">Past</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Booking Form -->
                <div class="lg:col-span-3">
                    <!-- Empty State -->
                    <div id="empty-state" class="card border-2 border-dashed border-gray-300 bg-gray-50">
                        <div class="card-body text-center py-16">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-100 rounded-full mb-4 animate-pulse">
                                <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Select a Date to Begin</h3>
                            <p class="text-gray-600 max-w-md mx-auto">Choose a date from the calendar to view available time slots and create your booking</p>
                            <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                                <span>Click any available date on the left</span>
                            </div>
                        </div>
                    </div>

                <!-- Booking Form (Hidden by default) -->
                <form action="{{ route('user.booking.submit') }}" method="POST" id="booking-form" class="hidden space-y-6" onsubmit="handleUserBookingSubmit(event, this)">
                    @csrf

                    <!-- User Info (Read-only) -->
                    <div class="card shadow-md hover:shadow-lg transition-all duration-200 animate-slide-in-up">
                        <div class="card-header bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <h2 class="card-title">Your Information</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" value="{{ $user->fname }} {{ $user->lname }}" class="form-input" readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" value="{{ $user->email }}" class="form-input" readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Phone</label>
                                    <input type="text" value="{{ $user->phone }}" class="form-input" readonly>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Address</label>
                                    <input type="text" value="{{ $user->address }}" class="form-input" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Branch Selection -->
                    <div class="card shadow-md hover:shadow-lg transition-all duration-200 animate-slide-in-up" style="animation-delay: 50ms;">
                        <div class="card-header bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h2 class="card-title">Select Branch</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Branch <span class="required">*</span></label>
                                <select name="admin_id" id="admin_id" class="form-select" required>
                                    <option value="">Choose your preferred branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch['id'] }}" 
                                                data-address="{{ $branch['address'] }}"
                                                data-phone="{{ $branch['phone'] }}">
                                            {{ $branch['name'] }} - {{ $branch['branch_name'] }} ({{ $branch['address'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-sm text-gray-500 mt-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Select the branch nearest to you or your preferred location
                                </p>
                                <div id="branch-info" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                                    <p class="text-sm font-semibold text-blue-900 mb-1">Branch Details:</p>
                                    <p class="text-sm text-blue-700" id="branch-address"></p>
                                    <p class="text-sm text-blue-700" id="branch-phone"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="card shadow-md hover:shadow-lg transition-all duration-200 animate-slide-in-up" style="animation-delay: 100ms;">
                        <div class="card-header bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h2 class="card-title">Booking Details</h2>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Date (Read-only) -->
                                <div class="form-group">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="booking_date" id="booking_date" class="form-input" required readonly>
                                </div>

                                <!-- Time -->
                                <div class="form-group">
                                    <label class="form-label">Time <span class="required">*</span></label>
                                    <select name="booking_time" id="booking_time" class="form-select" required>
                                        <option value="">Select time slot</option>
                                    </select>
                                </div>

                                <!-- Item Type -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Item Type <span class="required">*</span></label>
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

                                <!-- Pickup Address (Read-only) -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Pickup Address</label>
                                    <input type="text" name="pickup_address" value="{{ $user->address }}" class="form-input" required readonly>
                                    <input type="hidden" name="latitude" value="{{ $user->latitude }}">
                                    <input type="hidden" name="longitude" value="{{ $user->longitude }}">
                                </div>

                                <!-- Notes -->
                                <div class="form-group md:col-span-2">
                                    <label class="form-label">Special Instructions (Optional)</label>
                                    <textarea name="notes" id="notes" class="form-textarea" rows="3" placeholder="Any special requests or instructions..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Total Price -->
                        <div class="card-footer">
                            <div class="p-6 bg-gradient-to-r from-success-50 to-green-50 rounded-lg border-l-4 border-success shadow-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Price:</span>
                                    <span class="text-3xl font-bold text-success animate-pulse">₱<span id="total-price">0.00</span></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 mt-6">
                                <button type="button" id="clear-selection" class="btn btn-outline flex-1">Clear Selection</button>
                                <button type="submit" class="btn btn-primary flex-1">
                                    Submit Booking
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Pass data to JavaScript module
        window.bookingData = {
            services: @json($services),
            products: @json($products),
            routes: {
                slots: '{{ route('user.api.calendar.slots') }}',
                calculate: '{{ route('user.api.bookings.calculate') }}'
            }
        };

        // Handle booking form submission with UX feedback
        function handleUserBookingSubmit(event, form) {
            // Check if already submitting
            if (form.dataset.submitting === 'true') {
                event.preventDefault();
                return false;
            }

            // Mark as submitting
            form.dataset.submitting = 'true';

            // Get submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                // Store original text
                const originalText = submitBtn.innerHTML;
                
                // Disable button and show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-2 inline-block" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;

                // Safety timeout to re-enable if something goes wrong
                setTimeout(() => {
                    form.dataset.submitting = 'false';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            }

            return true;
        }
    </script>
    @vite(['resources/js/pages/user-booking.js'])
    @endpush
</x-layout>
