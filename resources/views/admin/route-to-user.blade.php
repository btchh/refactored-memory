<x-layout title="Track Customer Locations">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Custom marker styles */
        .custom-marker-user,
        .custom-marker-shop {
            background: transparent !important;
            border: none !important;
        }
        
        /* Pulse animation */
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
    </style>
    
    <div class="space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="Route to Customer"
            subtitle="Track customers and plan delivery routes"
            icon="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"
            gradient="indigo"
        />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Customer Selection -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6">
                        <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Customer List
                        </h2>
                        <p class="text-indigo-100 text-sm">Select a customer to plan route</p>
                    </div>
                    <div class="p-4">
                        <div id="user-list" class="space-y-3 max-h-96 overflow-y-auto">
                            <div class="flex items-center justify-center py-12">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600 mx-auto mb-4"></div>
                                    <p class="text-gray-500 text-sm">Loading customers...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Route Info -->
                <div id="route-info" class="bg-white rounded-2xl border border-gray-200 overflow-hidden hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 p-6">
                        <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            Delivery Route
                        </h2>
                        <p class="text-red-100 text-sm" id="customer-name">-</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 gap-3 mb-6">
                            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 text-center border border-indigo-200">
                                <svg class="w-5 h-5 text-indigo-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                <p class="text-xs text-indigo-600 font-medium mb-1">Distance</p>
                                <p class="text-lg font-bold text-indigo-700" id="distance">-</p>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 text-center border border-green-200">
                                <svg class="w-5 h-5 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-xs text-green-600 font-medium mb-1">Time</p>
                                <p class="text-lg font-bold text-green-700" id="travel-time">-</p>
                            </div>
                            <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4 text-center border border-amber-200">
                                <svg class="w-5 h-5 text-amber-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <p class="text-xs text-amber-600 font-medium mb-1">ETA</p>
                                <p class="text-lg font-bold text-amber-700" id="eta">-</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 mb-4">
                            <p class="text-xs text-gray-500 mb-1">Customer Address</p>
                            <p class="text-sm text-gray-700" id="customer-address">-</p>
                            <p class="text-sm text-gray-600 mt-1" id="customer-phone">-</p>
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
            userLocation: '{{ route('admin.api.users') }}'
        };
    </script>
    @vite(['resources/js/pages/route-to-user.js'])
</x-layout>
