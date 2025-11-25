<x-layout>
    <x-slot name="title">Shop Location</x-slot>

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl shadow-xl p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                    <span class="text-5xl">📍</span>
                </div>
                <div>
                    <h1 class="text-4xl font-bold mb-2">Shop Locations</h1>
                    <p class="text-lg opacity-90">Find the nearest laundry shop</p>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-200 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-2xl">🗺️</span>
                    <h2 class="text-2xl font-bold text-gray-800">Interactive Map</h2>
                </div>
                <div id="shopMap" class="w-full h-[500px] rounded-xl border-2 border-gray-300 shadow-inner"></div>
            </div>
        </div>

        <!-- Shop List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Sample Shop Cards -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-orange-100 hover:border-orange-300 hover:shadow-xl transition-all duration-300 p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="bg-orange-100 rounded-full w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="text-3xl">🏪</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">WashHour Main</h3>
                        <p class="text-sm text-gray-600">Main Branch</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-2">
                        <span class="text-lg">📍</span>
                        <p class="text-sm text-gray-700">123 Main Street, City Center</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📞</span>
                        <p class="text-sm text-gray-700">+63 912 345 6789</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⏰</span>
                        <p class="text-sm text-gray-700">8:00 AM - 8:00 PM</p>
                    </div>
                </div>
                <button class="mt-4 w-full btn btn-outline btn-sm hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">
                    Get Directions
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border-2 border-orange-100 hover:border-orange-300 hover:shadow-xl transition-all duration-300 p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="bg-orange-100 rounded-full w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="text-3xl">🏪</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">WashHour North</h3>
                        <p class="text-sm text-gray-600">North Branch</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-2">
                        <span class="text-lg">📍</span>
                        <p class="text-sm text-gray-700">456 North Avenue, Uptown</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📞</span>
                        <p class="text-sm text-gray-700">+63 912 345 6790</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⏰</span>
                        <p class="text-sm text-gray-700">7:00 AM - 9:00 PM</p>
                    </div>
                </div>
                <button class="mt-4 w-full btn btn-outline btn-sm hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">
                    Get Directions
                </button>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border-2 border-orange-100 hover:border-orange-300 hover:shadow-xl transition-all duration-300 p-6">
                <div class="flex items-start gap-4 mb-4">
                    <div class="bg-orange-100 rounded-full w-14 h-14 flex items-center justify-center flex-shrink-0">
                        <span class="text-3xl">🏪</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1">WashHour South</h3>
                        <p class="text-sm text-gray-600">South Branch</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-2">
                        <span class="text-lg">📍</span>
                        <p class="text-sm text-gray-700">789 South Road, Downtown</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">📞</span>
                        <p class="text-sm text-gray-700">+63 912 345 6791</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-lg">⏰</span>
                        <p class="text-sm text-gray-700">9:00 AM - 7:00 PM</p>
                    </div>
                </div>
                <button class="mt-4 w-full btn btn-outline btn-sm hover:bg-orange-500 hover:text-white hover:border-orange-500 transition-colors">
                    Get Directions
                </button>
            </div>
        </div>
    </div>

    <!-- Leaflet Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const map = L.map('shopMap').setView([13.941, 121.163], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(map);

            // Sample shop markers
            const shops = [
                { name: 'WashHour Main', lat: 13.941, lng: 121.163 },
                { name: 'WashHour North', lat: 13.951, lng: 121.173 },
                { name: 'WashHour South', lat: 13.931, lng: 121.153 }
            ];

            shops.forEach(shop => {
                L.marker([shop.lat, shop.lng])
                    .addTo(map)
                    .bindPopup(`<b>${shop.name}</b><br>Click for directions`);
            });
        });
    </script>
</x-layout>
