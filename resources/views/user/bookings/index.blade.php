<x-layout>
    <x-slot name="title">Book Laundry Service</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="Book Laundry Service"
            subtitle="Select a date and time to schedule your laundry"
            icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
            gradient="amber"
        />

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

                    <!-- Service Type Selection - Four Options -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">Choose Your Service</h2>
                        <p class="text-sm text-gray-500 mb-4">Select how you want your laundry picked up and delivered</p>
                        
                        <input type="hidden" name="pickup_method" id="pickup_method" value="branch_pickup">
                        <input type="hidden" name="delivery_method" id="delivery_method" value="branch_delivery">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Full Service -->
                            <label class="service-option-card cursor-pointer group" data-pickup="branch_pickup" data-delivery="branch_delivery">
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
                            <label class="service-option-card cursor-pointer group" data-pickup="customer_dropoff" data-delivery="branch_delivery">
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
                                            <p class="text-xs text-gray-500 mb-2">You drop off, we deliver</p>
                                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Save on Pickup</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Self Pickup -->
                            <label class="service-option-card cursor-pointer group" data-pickup="branch_pickup" data-delivery="customer_pickup">
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
                                            <p class="text-xs text-gray-500 mb-2">We pick up, you collect</p>
                                            <span class="inline-block px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded">Save on Delivery</span>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Self Service -->
                            <label class="service-option-card cursor-pointer group" data-pickup="customer_dropoff" data-delivery="customer_pickup">
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
                                            <p class="text-xs text-gray-500 mb-2">You drop off & collect</p>
                                            <span class="inline-block px-2 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded">Best Price</span>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <script>
                        // Update hidden fields when service option changes
                        document.querySelectorAll('.service-option-card').forEach(card => {
                            card.addEventListener('click', function() {
                                const pickup = this.dataset.pickup;
                                const delivery = this.dataset.delivery;
                                document.getElementById('pickup_method').value = pickup;
                                document.getElementById('delivery_method').value = delivery;
                                
                                // Show/hide pickup address based on pickup method
                                const pickupAddressField = document.getElementById('pickup-address-field');
                                if (pickupAddressField) {
                                    if (pickup === 'branch_pickup') {
                                        pickupAddressField.classList.remove('hidden');
                                    } else {
                                        pickupAddressField.classList.add('hidden');
                                    }
                                }
                            });
                        });
                    </script>

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
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Select Branch</h2>
                                <p class="text-sm text-gray-500">Choose your preferred laundry branch ({{ count($branches) }} available)</p>
                            </div>
                        </div>
                        
                        <!-- Hidden select for form submission -->
                        <select name="admin_id" id="admin_id" class="hidden" required>
                            <option value="">Choose your preferred branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch['id'] }}" data-address="{{ $branch['address'] }}" data-phone="{{ $branch['phone'] }}">
                                    {{ $branch['name'] }} - {{ $branch['branch_name'] }}
                                </option>
                            @endforeach
                        </select>
                        
                        <!-- Branch Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($branches as $branch)
                                <div class="branch-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-purple-400 hover:bg-purple-50/50 transition-all duration-200" 
                                     data-branch-id="{{ $branch['id'] }}"
                                     data-address="{{ $branch['address'] }}"
                                     data-phone="{{ $branch['phone'] }}">
                                    <div class="flex items-start gap-3">
                                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-bold text-gray-900 mb-1">{{ $branch['branch_name'] }}</h3>
                                            <p class="text-sm text-gray-600 mb-2">{{ $branch['name'] }}</p>
                                            <div class="space-y-1">
                                                <div class="flex items-start gap-2 text-xs text-gray-500">
                                                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="flex-1">{{ $branch['address'] }}</span>
                                                </div>
                                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <span>{{ $branch['phone'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="branch-check hidden">
                                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div id="branch-info" class="mt-4 p-4 bg-gradient-to-r from-purple-50 to-purple-100/50 border border-purple-200 rounded-xl hidden">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-purple-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-purple-900" id="branch-address"></p>
                                    <p class="text-sm text-purple-700" id="branch-phone"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pickup Address (Only shown for branch pickup) -->
                    <div id="pickup-address-field" class="bg-gradient-to-r from-blue-50 to-blue-100/50 rounded-xl border border-blue-200 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Pickup Location</h2>
                                <p class="text-sm text-gray-600">Where should we pick up your laundry?</p>
                            </div>
                        </div>
                        <input type="text" name="pickup_address" id="pickup_address" value="{{ $user->address }}" class="form-input bg-white/80 backdrop-blur border-blue-200 focus:border-blue-400 focus:ring-blue-400 rounded-xl" readonly>
                        <input type="hidden" name="latitude" id="latitude" value="{{ $user->latitude }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ $user->longitude }}">
                    </div>

                    <!-- Booking Details -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Booking Details</h2>
                                <p class="text-sm text-gray-500">Configure your laundry order</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Date & Time Row -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input type="date" name="booking_date" id="booking_date" class="form-input bg-gray-50 rounded-xl" required readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time <span class="text-red-500">*</span></label>
                                <select name="booking_time" id="booking_time" class="form-select rounded-xl" required>
                                    <option value="">Select time slot</option>
                                </select>
                            </div>
                            
                            <!-- Item Type -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Item Type <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="item_type" value="clothes" class="hidden peer">
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
                                <!-- Hidden select for form submission compatibility -->
                                <select name="item_type_select" id="item_type" class="hidden">
                                    <option value="">Select item type</option>
                                    <option value="clothes">Clothes</option>
                                    <option value="comforter">Comforter</option>
                                    <option value="shoes">Shoes</option>
                                </select>
                            </div>
                            
                            <!-- Services -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Services</label>
                                <div id="services-container" class="grid grid-cols-2 gap-3">
                                    <p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">Select a branch and item type first</p>
                                </div>
                            </div>
                            
                            <!-- Products -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Products</label>
                                <div id="products-container" class="grid grid-cols-2 gap-3">
                                    <p class="text-gray-400 col-span-2 text-sm text-center py-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">Select a branch and item type first</p>
                                </div>
                            </div>
                            
                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions (Optional)</label>
                                <textarea name="notes" id="notes" class="form-textarea rounded-xl" rows="2" placeholder="Any special requests or instructions..."></textarea>
                            </div>
                        </div>

                        <!-- Total & Actions -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 mb-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <span class="text-lg font-semibold text-gray-700">Total Amount</span>
                                    </div>
                                    <span class="text-3xl font-bold text-green-600">₱<span id="total-price">0.00</span></span>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" id="clear-selection" class="btn btn-outline flex-1 rounded-xl">Clear</button>
                                <button type="submit" class="btn btn-primary flex-1 rounded-xl">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Submit Booking
                                </button>
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
                calculate: '{{ route('user.api.bookings.calculate') }}',
                branchPricing: '{{ route('user.api.branch.pricing') }}'
            }
        };
    </script>
    @vite(['resources/js/pages/user-booking.js'])
    @endpush
</x-layout>
