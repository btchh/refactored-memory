<x-layout title="Track Shop Locations">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <div class="space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="Route to Shop"
            subtitle="Find and navigate to laundry shops near you"
            icon="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"
            gradient="teal"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Shop Selection -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Select Shop
                    </h2>
                    <div id="admin-list" class="space-y-3 max-h-80 overflow-y-auto">
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600"></div>
                        </div>
                    </div>
                </div>

                <!-- Route Info -->
                <div id="route-info" class="bg-white rounded-2xl border border-gray-200 p-6 hidden">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Route Information
                    </h2>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-teal-50 rounded-xl p-4 text-center">
                            <p class="text-xs text-teal-600 font-medium mb-1">Distance</p>
                            <p class="text-xl font-bold text-teal-700" id="distance">-</p>
                        </div>
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <p class="text-xs text-green-600 font-medium mb-1">Time</p>
                            <p class="text-xl font-bold text-green-700" id="travel-time">-</p>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <p class="text-xs text-blue-600 font-medium mb-1">ETA</p>
                            <p class="text-xl font-bold text-blue-700" id="eta">-</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 pt-4 mb-4">
                        <p class="text-sm text-gray-500 mb-1">Destination</p>
                        <p class="font-semibold text-gray-900" id="shop-name">-</p>
                        <p class="text-sm text-gray-500 mt-1" id="shop-address">-</p>
                        <p class="text-sm text-gray-500" id="shop-phone">-</p>
                    </div>
                    <a id="google-maps-link" href="#" target="_blank" class="w-full btn btn-primary flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Open in Google Maps
                    </a>
                </div>
            </div>

            <!-- Map -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div id="map" style="height: 550px; width: 100%;"></div>
                </div>
            </div>
        </div>
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
