<x-layout>
    <x-slot name="title">Book Laundry Service</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.card class="p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-1">Book Laundry Service</h1>
            <p class="text-gray-600">Select a date and time to schedule your laundry</p>
        </x-modules.card>

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

                    <!-- Service Type Selection - Intuitive Cards -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-2">How would you like to proceed?</h2>
                        <p class="text-sm text-gray-500 mb-4">Choose how you want your laundry handled</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Pickup Option -->
                            <label class="service-type-card cursor-pointer group">
                                <input type="radio" name="service_type" value="pickup" class="hidden" checked>
                                <div class="relative border-2 border-gray-200 rounded-xl p-5 transition-all duration-200 hover:border-blue-300 hover:shadow-md group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-50 group-has-[:checked]:shadow-lg">
                                    <!-- Selected indicator -->
                                    <div class="absolute top-3 right-3 w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-blue-500 group-has-[:checked]:bg-blue-500">
                                        <svg class="w-4 h-4 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="flex items-start gap-4">
                                        <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0 group-has-[:checked]:bg-blue-200">
                                            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 text-lg mb-1">Home Pickup</h3>
                                            <p class="text-sm text-gray-500 leading-relaxed">We'll come to your location to collect your laundry</p>
                                            <div class="mt-3 flex items-center gap-2 text-xs text-blue-600 font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span>Convenient doorstep service</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <!-- Drop-off Option -->
                            <label class="service-type-card cursor-pointer group">
                                <input type="radio" name="service_type" value="dropoff" class="hidden">
                                <div class="relative border-2 border-gray-200 rounded-xl p-5 transition-all duration-200 hover:border-green-300 hover:shadow-md group-has-[:checked]:border-green-500 group-has-[:checked]:bg-green-50 group-has-[:checked]:shadow-lg">
                                    <!-- Selected indicator -->
                                    <div class="absolute top-3 right-3 w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center group-has-[:checked]:border-green-500 group-has-[:checked]:bg-green-500">
                                        <svg class="w-4 h-4 text-white hidden group-has-[:checked]:block" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    
                                    <div class="flex items-start gap-4">
                                        <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0 group-has-[:checked]:bg-green-200">
                                            <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-bold text-gray-900 text-lg mb-1">Self Drop-off</h3>
                                            <p class="text-sm text-gray-500 leading-relaxed">Bring your laundry directly to our branch</p>
                                            <div class="mt-3 flex items-center gap-2 text-xs text-green-600 font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>Faster processing time</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

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
                                <p class="text-sm text-gray-500">Choose your preferred laundry branch</p>
                            </div>
                        </div>
                        <select name="admin_id" id="admin_id" class="form-select bg-gray-50 border-gray-200 focus:border-purple-400 focus:ring-purple-400 rounded-xl" required>
                            <option value="">Choose your preferred branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch['id'] }}" data-address="{{ $branch['address'] }}" data-phone="{{ $branch['phone'] }}">
                                    {{ $branch['name'] }} - {{ $branch['branch_name'] }}
                                </option>
                            @endforeach
                        </select>
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

                    <!-- Pickup Address (Only shown for pickup service type) -->
                    <div id="pickup-address-section" class="bg-gradient-to-r from-blue-50 to-blue-100/50 rounded-xl border border-blue-200 p-6">
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
