<x-layout title="Find Branches">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Full Screen Map Experience -->
    <div class="fixed inset-0 top-[72px] left-0 lg:left-64 bg-gray-900 z-[90]">
        <!-- Floating Stats Panel -->
        <div class="absolute top-6 right-6 z-[1000] flex gap-3">
            <!-- Total Branches -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-xl p-4 border border-white/20 min-w-[120px]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-wash/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900" id="stat-branches">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Branches</p>
                    </div>
                </div>
            </div>

            <!-- Nearest Branch -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-xl p-4 border border-white/20 min-w-[120px]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900" id="stat-nearest">-</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Nearest</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Search Panel -->
        <div class="absolute top-6 left-6 z-[1000] w-96 max-w-[calc(100vw-3rem)]">
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden border border-white/20">
                <!-- Header -->
                <div class="bg-gradient-to-r from-wash to-wash-dark p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-white">Find Branches</h1>
                            <p class="text-white/80 text-sm">Tap a branch to navigate</p>
                        </div>
                    </div>
                </div>

                <!-- Branch List -->
                <div id="admin-list" class="max-h-[60vh] overflow-y-auto p-4 space-y-2">
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-wash/20 border-t-wash rounded-full animate-spin mx-auto mb-3"></div>
                            <p class="text-gray-600 text-sm font-semibold">Loading branches...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Route Card -->
        <div id="route-info" class="absolute bottom-6 left-6 z-[1000] w-96 max-w-[calc(100vw-3rem)] hidden">
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden border border-white/20">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-black text-gray-900 mb-1" id="shop-name">-</h3>
                            <p class="text-sm text-gray-600">Branch Information</p>
                        </div>
                        <button onclick="document.getElementById('route-info').classList.add('hidden')" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Branch Details -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-xl p-4 mb-4 space-y-3 border-2 border-gray-200">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-wash/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-600 uppercase mb-1">Address</p>
                                <p class="text-xs text-gray-900 font-semibold leading-relaxed" id="shop-address">-</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-success/10 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-gray-600 uppercase mb-1">Phone</p>
                                <p class="text-xs text-gray-900 font-semibold" id="shop-phone">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <!-- Distance -->
                        <div class="group relative bg-white rounded-xl p-4 border-2 border-gray-200 hover:border-wash transition-all overflow-hidden">
                            <div class="absolute top-0 right-0 w-16 h-16 bg-wash/5 rounded-full -translate-y-1/2 translate-x-1/2 group-hover:scale-150 transition-transform"></div>
                            <div class="relative text-center">
                                <div class="w-8 h-8 bg-wash/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="w-8 h-8 bg-success/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                <div class="w-8 h-8 bg-warning/10 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-2xl font-black text-gray-900 mb-1" id="eta">-</p>
                                <p class="text-xs font-bold text-gray-600 uppercase">ETA</p>
                            </div>
                        </div>
                    </div>

                    <a id="google-maps-link" href="#" target="_blank" class="btn btn-primary w-full shadow-lg">
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
