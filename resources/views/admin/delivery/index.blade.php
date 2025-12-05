<x-layout title="Track Customer Locations">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Full Screen Map Experience -->
    <div class="fixed inset-0 top-[72px] left-0 lg:left-64 bg-gray-900 z-[90]">
        <!-- Floating Stats Panel -->
        <div class="absolute top-6 right-6 z-[1000] flex gap-3">
            <!-- Active Deliveries -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-xl p-4 border border-white/20 min-w-[120px]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-info/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900" id="stat-active">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Active</p>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-xl p-4 border border-white/20 min-w-[120px]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-wash/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900" id="stat-customers">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Customers</p>
                    </div>
                </div>
            </div>

            <!-- Avg Distance -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-xl p-4 border border-white/20 min-w-[120px]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-warning/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900" id="stat-distance">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Avg km</p>
                    </div>
                </div>
            </div>

            <!-- Avg ETA -->
            <div class="bg-white/95 backdrop-blur-xl rounded-xl shadow-xl p-4 border border-white/20 min-w-[120px]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-success/10 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-gray-900" id="stat-eta">0</p>
                        <p class="text-xs font-bold text-gray-600 uppercase">Avg min</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Customer List Panel -->
        <div class="absolute top-6 left-6 z-[1000] w-96 max-w-[calc(100vw-3rem)]">
            <div class="bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl overflow-hidden border border-white/20">
                <!-- Header -->
                <div class="bg-gradient-to-r from-wash to-wash-dark p-6">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-white">Deliveries</h1>
                            <p class="text-white/80 text-sm">Select a customer to navigate</p>
                        </div>
                    </div>
                </div>

                <!-- Customer List -->
                <div id="user-list" class="max-h-[60vh] overflow-y-auto p-4 space-y-2">
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <div class="w-12 h-12 border-4 border-wash/20 border-t-wash rounded-full animate-spin mx-auto mb-3"></div>
                            <p class="text-gray-600 text-sm font-semibold">Loading customers...</p>
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
                            <h3 class="text-lg font-black text-gray-900 mb-1" id="customer-name">-</h3>
                            <p class="text-sm text-gray-600">Route Information</p>
                        </div>
                        <button onclick="document.getElementById('route-info').classList.add('hidden')" class="w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-gradient-to-br from-wash/10 to-wash/5 rounded-xl p-3 text-center border border-wash/20">
                            <p class="text-2xl font-black text-wash mb-1" id="distance">-</p>
                            <p class="text-xs font-bold text-gray-600 uppercase">Distance</p>
                        </div>
                        <div class="bg-gradient-to-br from-success/10 to-success/5 rounded-xl p-3 text-center border border-success/20">
                            <p class="text-2xl font-black text-success mb-1" id="travel-time">-</p>
                            <p class="text-xs font-bold text-gray-600 uppercase">Time</p>
                        </div>
                        <div class="bg-gradient-to-br from-warning/10 to-warning/5 rounded-xl p-3 text-center border border-warning/20">
                            <p class="text-2xl font-black text-warning mb-1" id="eta">-</p>
                            <p class="text-xs font-bold text-gray-600 uppercase">ETA</p>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 mb-4">
                        <p class="text-xs font-bold text-gray-600 uppercase mb-2">Customer Details</p>
                        <p class="text-sm text-gray-600 mb-1" id="customer-address">-</p>
                        <p class="text-sm text-gray-600" id="customer-phone">-</p>
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
        // Pass configuration to JavaScript
        window.geoapifyApiKey = '{{ config('services.geoapify.api_key') }}';
        window.routes = {
            userLocation: '{{ route('admin.api.users') }}'
        };
    </script>
    @vite(['resources/js/pages/route-to-user.js'])
</x-layout>
