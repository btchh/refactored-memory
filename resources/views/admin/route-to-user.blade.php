<x-layout title="Route to User">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <x-nav type="admin" />
    
    <!-- Data attributes for JavaScript -->
    <div data-geoapify-key="{{ config('services.geoapify.api_key') }}" style="display: none;"></div>
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">Route to User</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Selection -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Select User</h2>
                        <div id="user-list" class="space-y-3 max-h-96 overflow-y-auto">
                            <div class="flex items-center justify-center py-8">
                                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="ml-3 text-gray-600">Loading users...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Route Info -->
                    <div id="route-info" class="bg-white rounded-lg shadow-md p-6 hidden">
                        <h2 class="text-xl font-semibold mb-4">Route Information</h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Distance</p>
                                <p class="text-2xl font-bold text-blue-600" id="distance">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Travel Time</p>
                                <p class="text-2xl font-bold text-green-600" id="travel-time">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estimated Arrival</p>
                                <p class="text-2xl font-bold text-purple-600" id="eta">-</p>
                                <p class="text-xs text-gray-400 mt-1" id="current-time"></p>
                            </div>
                            <div class="pt-4 border-t">
                                <p class="text-sm text-gray-600">Destination</p>
                                <p class="font-semibold" id="destination-name">-</p>
                                <p class="text-sm text-gray-500" id="destination-address">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div id="map" style="height: 600px; width: 100%;" class="rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</x-layout>
