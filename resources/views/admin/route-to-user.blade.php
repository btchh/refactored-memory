<x-layout title="Route to User">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <x-nav type="admin" />
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-6">Route to User</h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- User Selection -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4">Select User</h2>
                        <div id="user-list" class="space-y-3 max-h-96 overflow-y-auto">
                            <p class="text-gray-500">Loading users...</p>
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

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        let map;
        let routeLayer;
        let markers = [];
        const GEOAPIFY_API_KEY = '{{ config('services.geoapify.api_key') }}';

        // Initialize map
        function initMap() {
            const defaultCenter = [14.5995, 120.9842];
            
            map = L.map('map').setView(defaultCenter, 13);

            L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEOAPIFY_API_KEY}`, {
                attribution: '© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 20
            }).addTo(map);

            loadUsers();
        }

        // Load users
        function loadUsers() {
            fetch('/api/users')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayUsers(data.users);
                    }
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    document.getElementById('user-list').innerHTML = 
                        '<p class="text-red-500">Failed to load users</p>';
                });
        }

        // Display users
        function displayUsers(users) {
            const listContainer = document.getElementById('user-list');
            
            if (users.length === 0) {
                listContainer.innerHTML = '<p class="text-gray-500">No users found</p>';
                return;
            }

            listContainer.innerHTML = users.map(user => `
                <div class="p-4 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer transition" 
                     onclick="selectUser(${user.id})">
                    <h3 class="font-semibold">${user.name}</h3>
                    <p class="text-sm text-gray-600">${user.phone}</p>
                    <p class="text-xs text-gray-500">${user.address || 'No address'}</p>
                </div>
            `).join('');
        }

        // Select user and show route
        function selectUser(userId) {
            console.log('Selected user:', userId);
            
            fetch(`/admin/get-route/${userId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Route data:', data);
                    if (data.success) {
                        displayRoute(data);
                    } else {
                        alert(data.message || 'Failed to calculate route');
                    }
                })
                .catch(error => {
                    console.error('Error getting route:', error);
                    alert('Failed to calculate route: ' + error.message);
                });
        }

        // Display route on map
        function displayRoute(data) {
            // Clear existing markers and route
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            if (routeLayer) {
                map.removeLayer(routeLayer);
            }

            // Add admin marker (start)
            const adminMarker = L.marker([data.admin.latitude, data.admin.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #3B82F6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30]
                })
            }).addTo(map).bindPopup(`
                <div class="p-2">
                    <h3 class="font-bold">Your Location</h3>
                    <p class="text-sm">${data.admin.name}</p>
                    <p class="text-xs text-gray-500">${data.admin.address}</p>
                </div>
            `);
            markers.push(adminMarker);

            // Add user marker (destination)
            const userMarker = L.marker([data.user.latitude, data.user.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #EF4444; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30]
                })
            }).addTo(map).bindPopup(`
                <div class="p-2">
                    <h3 class="font-bold">Destination</h3>
                    <p class="text-sm">${data.user.name}</p>
                    <p class="text-xs text-gray-500">${data.user.address}</p>
                </div>
            `);
            markers.push(userMarker);

            // Draw route
            routeLayer = L.geoJSON(data.route.geometry, {
                style: {
                    color: '#3B82F6',
                    weight: 5,
                    opacity: 0.7
                }
            }).addTo(map);

            // Fit map to show entire route
            const bounds = routeLayer.getBounds();
            map.fitBounds(bounds, { padding: [50, 50] });

            // Update route info
            document.getElementById('route-info').classList.remove('hidden');
            document.getElementById('distance').textContent = data.route.distance_km + ' km';
            document.getElementById('travel-time').textContent = data.route.time_minutes + ' min';
            document.getElementById('eta').textContent = data.route.eta;
            document.getElementById('destination-name').textContent = data.user.name;
            document.getElementById('destination-address').textContent = data.user.address;
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</x-layout>
