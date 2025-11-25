<x-layout title="Track Admin Location">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <x-nav type="user" />
    
    <!-- Data attributes for JavaScript -->
    <div data-geoapify-key="{{ config('services.geoapify.api_key') }}" 
         data-user-name="{{ Auth::user()->fname }} {{ Auth::user()->lname }}"
         data-user-address="{{ Auth::user()->address }}"
         data-admin-location-route="{{ route('user.admin-location') }}"
         style="display: none;"></div>
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Track Admin Location</h1>
                <button onclick="refreshLocations()" id="refresh-btn" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Refresh Locations</span>
                </button>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div id="map" style="height: 500px; width: 100%;" class="rounded-lg"></div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Admin List</h2>
                <div id="admin-list" class="space-y-3">
                    <div class="flex items-center justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-3 text-gray-600">Loading admin locations...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</x-layout>
