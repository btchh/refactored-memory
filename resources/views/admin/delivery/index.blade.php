<x-layout title="Track Customer Locations">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <div class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-6">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Route to Customer</h1>
                <p class="text-gray-600">Track your customers and plan delivery routes</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Customer Selection -->
                <div class="lg:col-span-1">
                    <x-modules.card class="p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Select Customer</h2>
                        <div id="user-list" class="space-y-3 max-h-96 overflow-y-auto">
                            <p class="text-gray-500 text-center py-8">Loading customers...</p>
                        </div>
                    </x-modules.card>

                    <!-- Route Info -->
                    <div id="route-info" class="bg-white rounded-lg border border-gray-200 p-6 hidden">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Route Information</h2>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Distance</p>
                                <p class="text-2xl font-bold text-primary-600" id="distance">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Travel Time</p>
                                <p class="text-2xl font-bold text-success" id="travel-time">-</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estimated Arrival</p>
                                <p class="text-2xl font-bold text-gray-900" id="eta">-</p>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-1">Customer</p>
                                <p class="font-semibold text-gray-900" id="customer-name">-</p>
                                <p class="text-sm text-gray-500" id="customer-address">-</p>
                                <p class="text-sm text-gray-500" id="customer-phone">-</p>
                            </div>
                            <a id="google-maps-link" href="#" target="_blank" class="mt-4 w-full btn btn-primary btn-sm block text-center">
                                Open in Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <div id="map" style="height: 600px; width: 100%;" class="rounded-lg border border-gray-200"></div>
                    </div>
                </div>
            </div>
        </div>
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
