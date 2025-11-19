<x-layout title="Track Admin Location">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <x-nav type="user" />
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">Track Admin Location</h1>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div id="map" style="height: 500px; width: 100%;" class="rounded-lg"></div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Admin List</h2>
                <div id="admin-list" class="space-y-3">
                    <p class="text-gray-500">Loading admin locations...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map;
        let markers = [];
        const GEOAPIFY_API_KEY = '{{ env('GEOAPIFY_API_KEY') }}';

        // Initialize map
        function initMap() {
            // Default center (Philippines)
            const defaultCenter = [14.5995, 120.9842];
            
            map = L.map('map').setView(defaultCenter, 13);

            // Add Geoapify tile layer
            L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEOAPIFY_API_KEY}`, {
                attribution: '© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 20
            }).addTo(map);

            // Load admin locations
            loadAdminLocations();
        }

        // Load admin locations
        let userLocation = null;
        
        function loadAdminLocations() {
            console.log('Fetching admin locations...');
            fetch('{{ route('user.admin-location') }}')
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Data received:', data);
                    if (data.success) {
                        console.log('Number of admins:', data.admins.length);
                        userLocation = data.user_location;
                        updateMap(data.admins, userLocation);
                        updateAdminList(data.admins);
                    } else {
                        document.getElementById('admin-list').innerHTML = 
                            '<p class="text-red-500">Failed to load admin locations</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading admin locations:', error);
                    document.getElementById('admin-list').innerHTML = 
                        '<p class="text-red-500">Failed to load admin locations: ' + error.message + '</p>';
                });
        }

        // Update map with markers
        function updateMap(admins, userLoc) {
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Add user location marker if available
            if (userLoc) {
                const userMarker = L.marker([userLoc.latitude, userLoc.longitude], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<div style="background-color: #10B981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                        iconSize: [30, 30]
                    })
                }).addTo(map).bindPopup(`
                    <div class="p-2">
                        <h3 class="font-bold">Your Location</h3>
                        <p class="text-sm text-gray-600">{{ Auth::user()->fname }} {{ Auth::user()->lname }}</p>
                    </div>
                `);
                markers.push(userMarker);
            }

            if (admins.length === 0) {
                return;
            }

            // Add markers for each admin
            admins.forEach(admin => {
                const popupContent = `
                    <div class="p-2">
                        <h3 class="font-bold">${admin.name}</h3>
                        <p class="text-sm text-gray-600">${admin.phone}</p>
                        ${admin.distance_km !== undefined ? `
                            <div class="mt-2 text-xs">
                                <p class="text-blue-600">Distance: ${admin.distance_km} km</p>
                                <p class="text-green-600">Time: ${admin.eta_minutes} min</p>
                                <p class="text-purple-600">ETA: ${admin.eta}</p>
                            </div>
                        ` : ''}
                        <p class="text-xs text-gray-500 mt-1">Updated: ${admin.updated_at}</p>
                    </div>
                `;
                
                const marker = L.marker([admin.latitude, admin.longitude], {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<div style="background-color: #3B82F6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                        iconSize: [30, 30]
                    })
                }).addTo(map).bindPopup(popupContent);
                markers.push(marker);
            });

            // Fit map to show all markers
            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        // Update admin list
        function updateAdminList(admins) {
            const listContainer = document.getElementById('admin-list');
            
            if (admins.length === 0) {
                listContainer.innerHTML = '<p class="text-gray-500">No admin locations available</p>';
                return;
            }

            // Sort by distance if available
            if (admins[0].distance_km !== undefined) {
                admins.sort((a, b) => a.distance_km - b.distance_km);
            }

            listContainer.innerHTML = admins.map(admin => `
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" 
                     onclick="focusAdmin(${admin.latitude}, ${admin.longitude})">
                    <div class="flex-1">
                        <h3 class="font-semibold">${admin.name}</h3>
                        <p class="text-sm text-gray-600">${admin.phone}</p>
                        ${admin.distance_km !== undefined ? `
                            <div class="mt-2 flex gap-3 text-xs">
                                <span class="text-blue-600 font-semibold">📍 ${admin.distance_km} km</span>
                                <span class="text-green-600 font-semibold">⏱️ ${admin.eta_minutes} min</span>
                                <span class="text-purple-600 font-semibold">🕐 ETA: ${admin.eta}</span>
                            </div>
                        ` : ''}
                        <p class="text-xs text-gray-500 mt-1">Updated: ${admin.updated_at}</p>
                    </div>
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            `).join('');
        }

        // Focus on specific admin location
        function focusAdmin(lat, lng) {
            map.setView([lat, lng], 16);
        }

        // Auto-refresh every 30 seconds
        setInterval(loadAdminLocations, 30000);

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing map...');
            console.log('Leaflet available:', typeof L !== 'undefined');
            console.log('API Key:', GEOAPIFY_API_KEY);
            initMap();
        });
    </script>
</x-layout>
