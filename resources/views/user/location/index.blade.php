<x-layout title="Find Branches">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Full Screen Map Experience -->
    <div class="fixed inset-0 top-[72px] left-0 lg:left-64 bg-gray-900 z-[90]">
        <!-- Floating Stats Panel -->
        <div class="absolute top-4 sm:top-6 right-4 sm:right-6 z-[1000] flex flex-col sm:flex-row gap-2 sm:gap-3">
            <!-- Total Branches -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl p-3 sm:p-4 border-2 border-gray-200 min-w-[100px] sm:min-w-[120px]">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-wash to-wash-dark rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-black text-gray-900" id="stat-branches">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Branches</p>
                    </div>
                </div>
            </div>

            <!-- Nearest Branch -->
            <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl p-3 sm:p-4 border-2 border-gray-200 min-w-[100px] sm:min-w-[120px]">
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-success to-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xl sm:text-2xl font-black text-gray-900" id="stat-nearest">-</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Nearest</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Sidebar Panel -->
        <div class="absolute top-4 sm:top-6 left-4 sm:left-6 bottom-4 sm:bottom-6 z-[1000] w-[calc(100vw-2rem)] sm:w-96 max-w-[calc(100vw-2rem)] flex flex-col gap-4">
            <!-- Branch List Panel (Top Half - Fixed Height) -->
            <div class="h-[calc(50%-0.5rem)] bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-br from-wash to-wash-dark p-4 sm:p-5 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-black text-white">Find Branches</h1>
                            <p class="text-white/90 text-sm font-medium">Tap a branch to navigate</p>
                        </div>
                    </div>
                </div>

                <!-- Branch List -->
                <div id="admin-list" class="flex-1 overflow-y-auto p-3 sm:p-4 space-y-2">
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-wash/20 border-t-wash rounded-full animate-spin mx-auto mb-3"></div>
                            <p class="text-gray-600 text-sm font-semibold">Loading branches...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Info Panel (Bottom Half - Fixed Height) -->
            <div id="route-info" class="h-[calc(50%-0.5rem)] bg-white rounded-2xl shadow-2xl overflow-hidden border-2 border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="bg-gradient-to-br from-wash to-wash-dark p-4 sm:p-5 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-black text-white truncate" id="shop-name">-</h3>
                                <p class="text-white/90 text-sm font-medium">Branch Details</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-3">
                    <!-- Address -->
                    <div class="pb-3 border-b border-gray-200">
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Address</p>
                        <p class="text-sm text-gray-900 font-medium leading-relaxed" id="shop-address">-</p>
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
        window.geoapifyApiKey = '{{ config('services.geoapify.api_key') }}';
        window.routes = {
            adminLocation: '{{ route('user.api.admins') }}'
        };
    </script>
    @vite(['resources/js/pages/route-to-admin.js'])
</x-layout>
