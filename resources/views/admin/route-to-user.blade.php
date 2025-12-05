<x-layout title="Route to Customer">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative">
                <h1 class="text-5xl font-black text-white mb-3">Route to Customer</h1>
                <p class="text-xl text-white/80">Navigate to customer locations with real-time directions</p>
            </div>
        </div>

        <!-- Stat Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Active Deliveries -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-wash/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Active</p>
                    <p class="text-3xl font-black text-gray-900" id="stat-active">0</p>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-success/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-success" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Customers</p>
                    <p class="text-3xl font-black text-gray-900" id="stat-customers">0</p>
                </div>
            </div>

            <!-- Average Distance -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-warning/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Avg Distance</p>
                    <p class="text-3xl font-black text-gray-900"><span id="stat-distance">0</span> km</p>
                </div>
            </div>

            <!-- Average ETA -->
            <div class="group relative bg-white rounded-2xl p-6 border-2 border-gray-200 hover:border-info transition-all overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-info/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-info/10 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs text-gray-600 font-bold uppercase mb-1">Avg ETA</p>
                    <p class="text-3xl font-black text-gray-900"><span id="stat-eta">0</span> min</p>
                </div>
            </div>
        </div>

        <!-- Customer List Card -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    Customer Locations
                </h2>
            </div>

            <!-- Customer List -->
            <div id="user-list" class="space-y-3">
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <div class="w-12 h-12 border-4 border-wash/20 border-t-wash rounded-full animate-spin mx-auto mb-3"></div>
                        <p class="text-gray-600 text-sm font-semibold">Loading customers...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Card -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 overflow-hidden">
            <div class="p-6 border-b-2 border-gray-200">
                <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Interactive Map
                </h2>
            </div>
            <div id="map" class="w-full h-[600px]"></div>
        </div>

        <!-- Route Information Card -->
        <div id="route-info" class="bg-white rounded-2xl border-2 border-gray-200 p-6 hidden">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="text-2xl font-black text-gray-900 mb-1" id="customer-name">-</h3>
                    <p class="text-sm text-gray-600">Customer Information</p>
                </div>
                <button onclick="document.getElementById('route-info').classList.add('hidden')" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Customer Details -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl p-5 mb-6 space-y-4 border-2 border-gray-200">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-wash/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Address</p>
                        <p class="text-sm text-gray-900 font-semibold leading-relaxed" id="customer-address">-</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-success/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-1">Phone</p>
                        <p class="text-sm text-gray-900 font-semibold" id="customer-phone">-</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-4 mb-6">
                <!-- Distance -->
                <div class="group relative bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                    <div class="relative text-center">
                        <div class="w-10 h-10 bg-wash/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-black text-gray-900 mb-1" id="distance">-</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Distance</p>
                    </div>
                </div>

                <!-- Travel Time -->
                <div class="group relative bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-success transition-all overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-success/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                    <div class="relative text-center">
                        <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-black text-gray-900 mb-1" id="travel-time">-</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Time</p>
                    </div>
                </div>

                <!-- ETA -->
                <div class="group relative bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-warning transition-all overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-warning/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                    <div class="relative text-center">
                        <div class="w-10 h-10 bg-warning/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-black text-gray-900 mb-1" id="eta">-</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">ETA</p>
                    </div>
                </div>
            </div>

            <!-- Navigate Button -->
            <a id="google-maps-link" href="#" target="_blank" class="btn btn-primary w-full shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Navigate with Google Maps
            </a>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        window.geoapifyApiKey = '{{ config('services.geoapify.api_key') }}';
        window.routes = {
            userLocation: '{{ route('admin.api.users') }}'
        };
    </script>
    @vite(['resources/js/pages/route-to-user.js'])
</x-layout>
