<x-layout title="Track Customer Locations">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Full Screen Map Experience -->
    <div class="fixed inset-0 top-[72px] left-0 lg:left-64 bg-gray-900 z-[90]">
        <!-- Floating Stats Panel -->
        <div class="absolute top-4 sm:top-6 right-4 sm:right-6 z-[1000] flex flex-col sm:flex-row gap-2 sm:gap-3">
            <!-- Active Deliveries -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl p-3 sm:p-4 border-2 border-gray-200 min-w-[100px] sm:min-w-[120px]">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-wash to-wash-dark rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-black text-gray-900" id="stat-active">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Active</p>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl p-3 sm:p-4 border-2 border-gray-200 min-w-[100px] sm:min-w-[120px]">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-success to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-black text-gray-900" id="stat-customers">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Customers</p>
                    </div>
                </div>
            </div>

            <!-- Avg Distance -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl p-3 sm:p-4 border-2 border-gray-200 min-w-[100px] sm:min-w-[120px]">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-warning to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-black text-gray-900" id="stat-distance">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Avg km</p>
                    </div>
                </div>
            </div>

            <!-- Avg ETA -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl p-3 sm:p-4 border-2 border-gray-200 min-w-[100px] sm:min-w-[120px]">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-info to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-black text-gray-900" id="stat-eta">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Avg min</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Sidebar Panel -->
        <div class="absolute top-4 sm:top-6 left-4 sm:left-6 bottom-4 sm:bottom-6 z-[1000] w-[calc(100vw-2rem)] sm:w-96 max-w-[calc(100vw-2rem)] flex flex-col gap-4">
            <!-- Customer List Panel (Top Half - Fixed Height) -->
            <div class="h-[calc(50%-0.5rem)] bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-br from-wash to-wash-dark p-4 sm:p-5 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-black text-white">Deliveries</h1>
                            <p class="text-white/90 text-sm font-medium">Tap a customer to navigate</p>
                        </div>
                    </div>
                </div>

                <!-- Customer List -->
                <div id="user-list" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-2">
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-wash/20 border-t-wash rounded-full animate-spin mx-auto mb-3"></div>
                            <p class="text-gray-600 text-sm font-semibold">Loading customers...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Info Panel (Bottom Half - Fixed Height) -->
            <div id="route-info" class="h-[calc(50%-0.5rem)] bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-200 hidden flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-br from-wash to-wash-dark p-4 sm:p-5 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-black text-white truncate" id="customer-name">-</h3>
                                <p class="text-white/90 text-sm font-medium">Customer Details</p>
                            </div>
                        </div>
                        <button onclick="document.getElementById('route-info').classList.add('hidden'); document.getElementById('route-info').classList.remove('flex')" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors flex-shrink-0 ml-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3">
                    <!-- Address -->
                    <div class="pb-3 border-b border-gray-200">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Address</p>
                        <p class="text-sm text-gray-900 font-medium leading-relaxed" id="customer-address">-</p>
                    </div>

                    <!-- Route Info List -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-xs font-bold text-gray-500 uppercase">Distance</span>
                            <span class="text-base font-bold text-gray-900" id="distance">-</span>
                        </div>

                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-xs font-bold text-gray-500 uppercase">Travel Time</span>
                            <span class="text-base font-bold text-gray-900" id="travel-time">-</span>
                        </div>

                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-xs font-bold text-gray-500 uppercase">ETA</span>
                            <span class="text-base font-bold text-gray-900" id="eta">-</span>
                        </div>
                    </div>

                    <!-- Navigate Button -->
                    <a id="google-maps-link" href="#" target="_blank" class="btn btn-primary w-full shadow-xl hover:shadow-2xl mt-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Navigate with Google Maps
                    </a>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div id="map" class="w-full h-full"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Pass configuration to JavaScript
        window.geoapifyApiKey = '{{ config('services.geoapify.api_key') }}';
        window.routes = {
            userLocation: '{{ route('admin.api.users') }}'
        };
    </script>
    @vite(['resources/js/pages/route-to-user.js'])
</x-layout>
