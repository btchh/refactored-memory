/**
 * User Location Tracker
 * Tracks and displays user locations on a map with real-time updates
 */

export class UserTracker {
    constructor(options = {}) {
        this.mapId = options.mapId || 'map';
        this.apiKey = options.apiKey || '';
        this.fetchUrl = options.fetchUrl || '';
        this.refreshInterval = options.refreshInterval || 30000; // 30 seconds
        this.map = null;
        this.markers = [];
        this.adminLocation = null;
        this.intervalId = null;
        this.users = [];
        this.routeLayer = null;
        
        this.init();
    }

    init() {
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            return;
        }

        // Initialize map
        const defaultCenter = [14.5995, 120.9842];
        this.map = L.map(this.mapId).setView(defaultCenter, 13);

        // Add tile layer
        L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${this.apiKey}`, {
            attribution: '© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 20
        }).addTo(this.map);

        // Load user locations
        this.loadUserLocations();

        // Set up auto-refresh
        this.startAutoRefresh();
    }

    async loadUserLocations() {
        try {
            const response = await fetch(this.fetchUrl);
            const data = await response.json();
            
            if (data.success) {
                this.adminLocation = data.admin_location;
                this.updateMap(data.users, this.adminLocation);
                this.updateUserList(data.users);
            } else {
                this.showError('Failed to load user locations');
            }
        } catch (error) {
            console.error('Error loading user locations:', error);
            this.showError('Failed to load user locations: ' + error.message);
        }
    }

    updateMap(users, adminLoc) {
        // Clear existing markers
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];

        // Add admin location marker if available
        if (adminLoc) {
            const adminMarker = L.marker([adminLoc.latitude, adminLoc.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #10B981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30]
                })
            }).addTo(this.map).bindPopup(`
                <div class="p-2">
                    <h3 class="font-bold">Your Location</h3>
                    <p class="text-sm text-gray-600">Admin</p>
                </div>
            `);
            this.markers.push(adminMarker);
        }

        if (users.length === 0) {
            return;
        }

        // Add markers for each user
        users.forEach(user => {
            const popupContent = `
                <div class="p-2">
                    <h3 class="font-bold">${user.name}</h3>
                    <p class="text-sm text-gray-600">${user.phone}</p>
                    <p class="text-xs text-gray-500">${user.address}</p>
                    ${user.distance_km !== undefined ? `
                        <div class="mt-2 text-xs">
                            <p class="text-blue-600">Distance: ${user.distance_km} km</p>
                            <p class="text-green-600">Time: ${user.eta_minutes} min</p>
                            <p class="text-purple-600">ETA: ${user.eta}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            const marker = L.marker([user.latitude, user.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #EF4444; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30]
                })
            }).addTo(this.map).bindPopup(popupContent);
            this.markers.push(marker);
        });

        // Fit map to show all markers
        if (this.markers.length > 0) {
            const group = L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    updateUserList(users) {
        const listContainer = document.getElementById('user-list');
        if (!listContainer) return;
        
        if (users.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500 text-center py-8">No customers have booked with your branch yet</p>';
            return;
        }

        // Sort by distance if available
        if (users[0].distance_km !== undefined) {
            users.sort((a, b) => a.distance_km - b.distance_km);
        }

        // Store users for later use
        this.users = users;

        listContainer.innerHTML = users.map(user => `
            <button 
                onclick="window.userTracker.selectUser(${user.id})"
                class="user-item w-full text-left p-4 border border-gray-200 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-all duration-200"
                data-user-id="${user.id}"
            >
                <div class="flex items-start gap-3">
                    <div class="bg-red-100 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 truncate">${user.name}</h3>
                        <p class="text-sm text-gray-600 truncate">${user.phone}</p>
                        <p class="text-xs text-gray-500 mt-1">${user.address || 'No address'}</p>
                        ${user.distance_km !== undefined ? `
                            <div class="mt-2 flex gap-2 text-xs">
                                <span class="text-blue-600 font-semibold">📍 ${user.distance_km} km</span>
                                <span class="text-green-600 font-semibold">⏱️ ${user.eta_minutes} min</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </button>
        `).join('');
    }

    async selectUser(userId) {
        const user = this.users.find(u => u.id === userId);
        if (!user) {
            console.error('User not found:', userId);
            return;
        }
        
        if (!this.adminLocation) {
            console.error('Admin location not available');
            return;
        }

        console.log('Selecting user:', user);
        console.log('Admin location:', this.adminLocation);

        // Clear existing route layer
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
        }

        // Fetch actual route from Geoapify
        try {
            const routeData = await this.fetchRoute(
                this.adminLocation.latitude,
                this.adminLocation.longitude,
                user.latitude,
                user.longitude
            );

            if (routeData && routeData.features && routeData.features[0]) {
                console.log('Drawing route from API data');
                // Draw the actual route
                this.routeLayer = L.geoJSON(routeData.features[0].geometry, {
                    style: {
                        color: '#EF4444',
                        weight: 6,
                        opacity: 0.9,
                        lineJoin: 'round',
                        lineCap: 'round'
                    }
                }).addTo(this.map);

                // Fit map to show entire route with better padding
                setTimeout(() => {
                    this.map.fitBounds(this.routeLayer.getBounds(), { 
                        padding: [50, 50],
                        maxZoom: 14
                    });
                }, 100);
            } else {
                console.log('Route data invalid, falling back to straight line');
                // Fallback to straight line if routing fails
                this.drawStraightLine(user);
            }
        } catch (error) {
            console.error('Error fetching route:', error);
            // Fallback to straight line
            this.drawStraightLine(user);
        }

        // Update route info panel
        this.updateRouteInfo(user);

        // Highlight selected user
        this.highlightSelectedUser(userId);

        // Open popup for selected user
        this.markers.forEach(marker => {
            const markerLatLng = marker.getLatLng();
            if (markerLatLng.lat === user.latitude && markerLatLng.lng === user.longitude) {
                marker.openPopup();
            }
        });
    }

    updateRouteInfo(user) {
        const routeInfo = document.getElementById('route-info');
        if (!routeInfo) return;

        // Show the panel
        routeInfo.classList.remove('hidden');

        // Update the information
        document.getElementById('distance').textContent = user.distance_km ? `${user.distance_km} km` : '-';
        document.getElementById('travel-time').textContent = user.eta_minutes ? `${user.eta_minutes} min` : '-';
        document.getElementById('eta').textContent = user.eta || '-';
        document.getElementById('customer-name').textContent = user.name;
        document.getElementById('customer-address').textContent = user.address || '-';
        document.getElementById('customer-phone').textContent = user.phone || '-';

        // Update Google Maps link
        const googleMapsLink = document.getElementById('google-maps-link');
        if (googleMapsLink && this.adminLocation) {
            const url = `https://www.google.com/maps/dir/?api=1&origin=${this.adminLocation.latitude},${this.adminLocation.longitude}&destination=${user.latitude},${user.longitude}`;
            googleMapsLink.href = url;
        }
    }

    async fetchRoute(startLat, startLng, endLat, endLng) {
        try {
            const url = `https://api.geoapify.com/v1/routing?waypoints=${startLat},${startLng}|${endLat},${endLng}&mode=drive&apiKey=${this.apiKey}`;
            console.log('Fetching route from:', url);
            
            const response = await fetch(url);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Routing API error:', response.status, errorText);
                throw new Error(`Routing API request failed: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Route data received:', data);
            
            // Check if route data is valid
            if (!data.features || !data.features[0] || !data.features[0].geometry) {
                console.error('Invalid route data structure:', data);
                return null;
            }
            
            return data;
        } catch (error) {
            console.error('Error fetching route from Geoapify:', error);
            return null;
        }
    }

    drawStraightLine(user) {
        this.routeLayer = L.polyline([
            [this.adminLocation.latitude, this.adminLocation.longitude],
            [user.latitude, user.longitude]
        ], {
            color: '#EF4444',
            weight: 4,
            opacity: 0.7,
            dashArray: '10, 10'
        }).addTo(this.map);

        const bounds = L.latLngBounds([
            [this.adminLocation.latitude, this.adminLocation.longitude],
            [user.latitude, user.longitude]
        ]);
        this.map.fitBounds(bounds, { padding: [80, 80] });
    }

    highlightSelectedUser(userId) {
        // Remove previous highlights
        document.querySelectorAll('.user-item').forEach(item => {
            item.classList.remove('border-primary-500', 'bg-primary-50');
            item.classList.add('border-gray-200');
        });

        // Highlight selected user
        const selectedItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (selectedItem) {
            selectedItem.classList.remove('border-gray-200');
            selectedItem.classList.add('border-primary-500', 'bg-primary-50');
        }
    }

    focusUser(lat, lng) {
        if (this.map) {
            this.map.setView([lat, lng], 16);
        }
    }

    showError(message) {
        const listContainer = document.getElementById('user-list');
        if (listContainer) {
            listContainer.innerHTML = `<p class="text-red-500">${message}</p>`;
        }
    }

    startAutoRefresh() {
        this.intervalId = setInterval(() => {
            this.loadUserLocations();
        }, this.refreshInterval);
    }

    stopAutoRefresh() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
    }

    destroy() {
        this.stopAutoRefresh();
        if (this.map) {
            this.map.remove();
        }
    }
}
