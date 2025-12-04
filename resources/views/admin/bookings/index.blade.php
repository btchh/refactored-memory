<x-layout>
    <x-slot name="title">Booking Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-8 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur rounded-xl p-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-1">Booking Management</h1>
                        <p class="text-white/80">Create and manage customer bookings</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar Panel -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Select Date
                    </h2>
                    
                    <div id="calendar-container" class="mb-4">
                        <div class="flex justify-between items-center mb-4">
                            <button id="prev-month" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <h3 id="current-month" class="text-lg font-semibold text-gray-800"></h3>
                            <button id="next-month" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
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
                <div id="date-bookings-section" class="bg-white rounded-xl border border-gray-200 shadow-sm hidden">
                    <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-900" id="selected-date-display"></h2>
                                <p class="text-sm text-gray-500"><span id="bookings-count-badge" class="font-semibold text-primary-600">0</span> booking(s)</p>
                            </div>
                        </div>
                        <button type="button" id="show-add-form-btn" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Booking
                        </button>
                    </div>
                    <div class="p-5">
                        <div id="date-bookings-list" class="space-y-3"></div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Select a Date</h3>
                    <p class="text-gray-500">Choose a date from the calendar to view or create bookings</p>
                </div>

                <!-- Booking Form -->
                <form action="{{ route('admin.bookings.store') }}" method="POST" id="booking-form" class="hidden space-y-6">
                    @csrf

                    <!-- Service Type Selection -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">Choose Service Type</h2>
                        <p class="text-sm text-gray-500 mb-4">Select how the customer's laundry will be picked up and delivered</p>
                        
                        <input type="hidden" name="pickup_method" id="admin_pickup_method" value="branch_pickup">
                        <input type="hidden" name="delivery_method" id="admin_delivery_method" value="branch_delivery">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Full Service -->
                            <label class="admin-service-card cursor-pointer group" data-pickup="branch_pickup" data-delivery="branch_delivery">
                                <input type="radio" name="service_option" value="full_service" class="hidden" checked>
                                <div class="relative border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-blue-300 hover:shadow-md group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-50 group-has-[:checked]:shadow-lg">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500">
                                        <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 group-has-[:checked]:bg-blue-200">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 mb-1">Full Service</h3>
                                            <p class="text-xs text-gray-500 mb-2">We pick up & deliver</p>
                                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Most Convenient</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Self Drop-off -->
                            <label class="admin-service-card cursor-pointer group" data-pickup="customer_dropoff" data-delivery="branch_delivery">
                                <input type="radio" name="service_option" value="self_dropoff" class="hidden">
                                <div class="relative border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-green-300 hover:shadow-md group-has-[:checked]:border-green-500 group-has-[:checked]:bg-green-50 group-has-[:checked]:shadow-lg">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-green-500 group-has-[:checked]:bg-green-500">
                                        <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 group-has-[:checked]:bg-green-200">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 mb-1">Self Drop-off</h3>
                                            <p class="text-xs text-gray-500 mb-2">Customer drops off, we deliver</p>
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Save on Pickup</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Self Pickup -->
                            <label class="admin-service-card cursor-pointer group" data-pickup="branch_pickup" data-delivery="customer_pickup">
                                <input type="radio" name="service_option" value="self_pickup" class="hidden">
                                <div class="relative border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-purple-300 hover:shadow-md group-has-[:checked]:border-purple-500 group-has-[:checked]:bg-purple-50 group-has-[:checked]:shadow-lg">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-purple-500 group-has-[:checked]:bg-purple-500">
                                        <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 group-has-[:checked]:bg-purple-200">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 mb-1">Self Pickup</h3>
                                            <p class="text-xs text-gray-500 mb-2">We pick up, customer collects</p>
                                            <span class="inline-block px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded">Save on Delivery</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Self Service -->
                            <label class="admin-service-card cursor-pointer group" data-pickup="customer_dropoff" data-delivery="customer_pickup">
                                <input type="radio" name="service_option" value="self_service" class="hidden">
                                <div class="relative border-2 border-gray-200 rounded-xl p-4 transition-all duration-200 hover:border-orange-300 hover:shadow-md group-has-[:checked]:border-orange-500 group-has-[:checked]:bg-orange-50 group-has-[:checked]:shadow-lg">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-orange-500 group-has-[:checked]:bg-orange-500">
                                        <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0 group-has-[:checked]:bg-orange-200">
                                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 mb-1">Self Service</h3>
                                            <p class="text-xs text-gray-500 mb-2">Customer drops off & collects</p>
                                            <span class="inline-block px-2 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded">Best Price</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <script>
                        // Update hidden fields for admin booking
                        document.querySelectorAll('.admin-service-card').forEach(card => {
                            card.addEventListener('click', function() {
                                document.getElementById('admin_pickup_method').value = this.dataset.pickup;
                                document.getElementById('admin_delivery_method').value = this.dataset.delivery;
                            });
                        });
                    </script>

                    <!-- Booking Type Selection -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Booking Type
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer group">
                                <input type="radio" name="booking_type" value="online" class="hidden" checked>
                                <div class="relative border-2 border-gray-200 rounded-xl p-4 transition-all hover:border-blue-300 group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-50">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500">
                                        <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-has-[:checked]:bg-blue-200">
                                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900">Online Booking</h3>
                                            <p class="text-xs text-gray-500">Registered customer</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="cursor-pointer group">
                                <input type="radio" name="booking_type" value="walkin" class="hidden" id="walkin-radio">
                                <div class="relative border-2 border-gray-200 rounded-xl p-4 transition-all hover:border-purple-300 group-has-[:checked]:border-purple-500 group-has-[:checked]:bg-purple-50">
                                    <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-purple-500 group-has-[:checked]:bg-purple-500">
                                        <svg class="w-3 h-3 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center group-has-[:checked]:bg-purple-200">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900">Walk-in</h3>
                                            <p class="text-xs text-gray-500">Customer at branch</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Customer Selection -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm" id="customer-selection-section">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Select Customer <span class="text-red-500" id="customer-required-indicator">*</span>
                        </h2>
                        <p class="text-sm text-gray-500 mb-3" id="customer-help-text">
                            <span id="online-help">Select the customer who made this booking</span>
                            <span id="walkin-help" class="hidden">Optional: Link to existing customer or leave blank for guest</span>
                        </p>
                        <div class="relative">
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Select a customer (or leave blank for guest walk-in) --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            data-email="{{ $user->email }}" 
                                            data-phone="{{ $user->phone }}">
                                        {{ $user->fname }} {{ $user->lname }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="guest-indicator" class="hidden mt-2 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                <div class="flex items-center gap-2 text-purple-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium">Guest walk-in (no customer account)</span>
                                </div>
                            </div>
                        </div>
                        
                        <div id="selected-user-info" class="mt-4 p-4 bg-primary-50 border border-primary-200 rounded-xl hidden">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 bg-primary-200 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-primary-900" id="selected-user-name"></p>
                                    <p class="text-sm text-primary-700" id="selected-user-email"></p>
                                    <p class="text-sm text-primary-700" id="selected-user-phone"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        // Handle booking type change (Online vs Walk-in)
                        document.querySelectorAll('input[name="booking_type"]').forEach(radio => {
                            radio.addEventListener('change', function() {
                                const userSelect = document.getElementById('user_id');
                                const requiredIndicator = document.getElementById('customer-required-indicator');
                                const onlineHelp = document.getElementById('online-help');
                                const walkinHelp = document.getElementById('walkin-help');
                                const guestIndicator = document.getElementById('guest-indicator');
                                const userInfo = document.getElementById('selected-user-info');
                                
                                if (this.value === 'walkin') {
                                    // Walk-in: Customer is optional
                                    userSelect.required = false;
                                    requiredIndicator.classList.add('hidden');
                                    onlineHelp.classList.add('hidden');
                                    walkinHelp.classList.remove('hidden');
                                    
                                    // Show guest indicator if no customer selected
                                    if (!userSelect.value) {
                                        guestIndicator.classList.remove('hidden');
                                        userInfo.classList.add('hidden');
                                    }
                                } else {
                                    // Online: Customer is required
                                    userSelect.required = true;
                                    requiredIndicator.classList.remove('hidden');
                                    onlineHelp.classList.remove('hidden');
                                    walkinHelp.classList.add('hidden');
                                    guestIndicator.classList.add('hidden');
                                }
                            });
                        });
                        
                        // Show user info when customer is selected
                        document.getElementById('user_id').addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            const userInfo = document.getElementById('selected-user-info');
                            const guestIndicator = document.getElementById('guest-indicator');
                            const isWalkin = document.getElementById('walkin-radio').checked;
                            
                            if (this.value) {
                                document.getElementById('selected-user-name').textContent = selectedOption.text.split(' (')[0];
                                document.getElementById('selected-user-email').textContent = selectedOption.dataset.email;
                                document.getElementById('selected-user-phone').textContent = selectedOption.dataset.phone;
                                userInfo.classList.remove('hidden');
                                guestIndicator.classList.add('hidden');
                            } else {
                                userInfo.classList.add('hidden');
                                // Show guest indicator only for walk-in
                                if (isWalkin) {
                                    guestIndicator.classList.remove('hidden');
                                }
                            }
                        });
                    </script>

                    <!-- Pickup Address (for pickup service type) -->
                    <div id="pickup-address-section" class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Pickup Location</h2>
                                <p class="text-sm text-gray-500">Customer's address for pickup</p>
                            </div>
                        </div>
                        <input type="text" name="pickup_address" id="pickup_address" class="form-input bg-gray-50" required readonly>
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Booking Details
                        </h2>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Item Type <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="item_type" value="clothes" class="hidden peer" required>
                                        <div class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-orange-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Clothes</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="item_type" value="comforter" class="hidden peer">
                                        <div class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-orange-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Comforter</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="item_type" value="shoes" class="hidden peer">
                                        <div class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-xl transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300">
                                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-orange-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">Shoes</span>
                                        </div>
                                    </label>
                                </div>
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
                                <span class="text-3xl font-bold text-green-600">₱<span id="total-price">0.00</span></span>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" id="clear-form" class="btn btn-outline flex-1">Clear</button>
                                <button type="submit" class="btn btn-primary flex-1">Create Booking</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- User's Bookings List -->
                <div id="user-bookings-section" class="bg-white rounded-xl border border-gray-200 shadow-sm hidden">
                    <div class="p-5 border-b border-gray-200">
                        <h2 class="font-bold text-gray-900"><span id="bookings-user-name"></span>'s Bookings</h2>
                    </div>
                    <div class="p-5">
                        <div id="user-bookings-list" class="space-y-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Weight Modal -->
    <dialog id="edit-weight-modal" class="modal rounded-xl">
        <div class="modal-box bg-white rounded-xl shadow-xl max-w-md w-full p-6">
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
    <dialog id="view-booking-modal" class="modal rounded-xl">
        <div class="modal-box bg-white rounded-xl shadow-xl max-w-2xl w-full p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Details</h3>
            <div id="view-booking-content"></div>
            <div class="mt-4 flex justify-end">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('view-booking-modal').close()">Close</button>
            </div>
        </div>
    </dialog>

    <!-- Reschedule Modal -->
    <dialog id="reschedule-modal" class="modal rounded-xl">
        <div class="modal-box bg-white rounded-xl shadow-xl max-w-md w-full p-6">
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
