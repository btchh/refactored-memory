<x-layout title="Track Shop Locations">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Custom marker styles */
        .custom-marker-user,
        .custom-marker-shop {
            background: transparent !important;
            border: none !important;
        }
        
        /* Pulse animation for user location */
        @keyframes pulse {
            0%, 100% {
                transform: translateX(-50%) scale(1);
                opacity: 0.7;
            }
            50% {
                transform: translateX(-50%) scale(2);
                opacity: 0;
            }
        }
        
        /* Custom popup styles */
        .custom-popup .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            padding: 0;
            overflow: hidden;
        }
        
        .custom-popup .leaflet-popup-content {
            margin: 0;
            min-width: 200px;
        }
        
        .custom-popup .leaflet-popup-tip {
            background: white;
        }
        
        /* Ensure popups appear above everything */
        .leaflet-popup-pane {
            z-index: 700 !important;
        }
        
        .leaflet-marker-pane {
            z-index: 600 !important;
        }
        
        /* Map container */
        #map {
            z-index: 1;
        }
    </style>
    
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
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-teal-500 to-teal-600 p-6">
                        <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Nearby Branches
                        </h2>
                        <p class="text-teal-100 text-sm">Select a branch to view route</p>
                    </div>
                    <div class="p-4">
                        <div id="admin-list" class="space-y-3 max-h-96 overflow-y-auto">
                            <div class="flex items-center justify-center py-12">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-teal-200 border-t-teal-600 mx-auto mb-4"></div>
                                    <p class="text-gray-500 text-sm">Loading branches...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Route Info -->
                <div id="route-info" class="bg-white rounded-2xl border border-gray-200 overflow-hidden hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                        <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Route Details
                        </h2>
                        <p class="text-blue-100 text-sm" id="shop-name">-</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-4 text-center border border-teal-200">
                                <svg class="w-5 h-5 text-teal-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <p class="text-xs text-teal-600 font-medium mb-1">Distance</p>
                                <p class="text-lg font-bold text-teal-700" id="distance">-</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 text-center border border-green-200">
                                <svg class="w-5 h-5 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-green-600 font-medium mb-1">Time</p>
                                <p class="text-lg font-bold text-green-700" id="travel-time">-</p>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 text-center border border-purple-200">
                                <svg class="w-5 h-5 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="text-xs text-purple-600 font-medium mb-1">ETA</p>
                                <p class="text-lg font-bold text-purple-700" id="eta">-</p>
                            </div>
                        </div>
                        <a id="google-maps-link" href="#" target="_blank" class="w-full btn btn-primary flex items-center justify-center gap-2 shadow-lg hover:shadow-xl transition-shadow">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Navigate with Google Maps
                        </a>
                    </div>
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
