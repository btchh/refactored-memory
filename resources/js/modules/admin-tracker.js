/**
 * Admin Location Tracker (for Users)
 * Tracks and displays admin/shop locations on a map with routing
 */

export class AdminTracker {
    constructor(options = {}) {
        this.mapId = options.mapId || 'map';
        this.apiKey = options.apiKey || '';
        this.fetchUrl = options.fetchUrl || '';
        this.refreshInterval = options.refreshInterval || 30000; // 30 seconds
        this.map = null;
        this.markers = [];
        this.userLocation = null;
        this.intervalId = null;
        this.admins = [];
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

        // Load admin locations
        this.loadAdminLocations();

        // Set up auto-refresh
        this.startAutoRefresh();
    }

    async loadAdminLocations() {
        try {
            const response = await fetch(this.fetchUrl);
            const data = await response.json();
            
            if (data.success) {
                this.userLocation = data.user_location;
                this.updateMap(data.admins, this.userLocation);
                this.updateAdminList(data.admins);
            } else {
                this.showError('Failed to load admin locations');
            }
        } catch (error) {
            console.error('Error loading admin locations:', error);
            this.showError('Failed to load admin locations: ' + error.message);
        }
    }

    updateMap(admins, userLoc) {
        // Clear existing markers
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];

        // Add user location marker if available
        if (userLoc) {
            const userMarker = L.marker([userLoc.latitude, userLoc.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background-color: #10B981; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                    iconSize: [30, 30]
                })
            }).addTo(this.map).bindPopup(`
                <div class="p-2">
                    <h3 class="font-bold">Your Location</h3>
                    <p class="text-sm text-gray-600">${userLoc.name || 'User'}</p>
                </div>
            `);
            this.markers.push(userMarker);
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
            }).addTo(this.map).bindPopup(popupContent);
            this.markers.push(marker);
        });

        // Fit map to show all markers
        if (this.markers.length > 0) {
            const group = L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    updateAdminList(admins) {
        const listContainer = document.getElementById('admin-list');
        if (!listContainer) return;
        
        if (admins.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500 text-center py-8">No shops available</p>';
            return;
        }

        // Sort by distance if available
        if (admins[0].distance_km !== undefined) {
            admins.sort((a, b) => a.distance_km - b.distance_km);
        }

        // Store admins for later use
        this.admins = admins;

        listContainer.innerHTML = admins.map(admin => `
            <button 
                onclick="window.adminTracker.selectAdmin(${admin.id})"
                class="admin-item w-full text-left p-4 border border-gray-200 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-all duration-200"
                data-admin-id="${admin.id}"
            >
                <div class="flex items-start gap-3">
                    <div class="bg-blue-100 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 truncate">${admin.name}</h3>
                        <p class="text-sm text-gray-600 truncate">${admin.phone}</p>
                        ${admin.distance_km !== undefined ? `
                            <div class="mt-2 flex gap-2 text-xs">
                                <span class="text-blue-600 font-semibold">📍 ${admin.distance_km} km</span>
                                <span class="text-green-600 font-semibold">⏱️ ${admin.eta_minutes} min</span>
                            </div>
                        ` : ''}
                        <p class="text-xs text-gray-500 mt-1">Updated: ${admin.updated_at}</p>
                    </div>
                </div>
            </button>
        `).join('');
    }

    async selectAdmin(adminId) {
        const admin = this.admins.find(a => a.id === adminId);
        if (!admin || !this.userLocation) {
            console.error('Admin or user location not found', { adminId, admin, userLocation: this.userLocation });
            return;
        }

        // Clear existing route layer
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
        }

        // Fetch actual route from Geoapify
        try {
            console.log('Fetching route for admin:', admin.name);
            const routeData = await this.fetchRoute(
                this.userLocation.latitude,
                this.userLocation.longitude,
                admin.latitude,
                admin.longitude
            );

            if (routeData && routeData.features && routeData.features.length > 0 && routeData.features[0].geometry) {
                console.log('Drawing route from API data', routeData.features[0].geometry);
                
                try {
                    // Draw the actual route using GeoJSON (just the geometry, like admin side)
                    this.routeLayer = L.geoJSON(routeData.features[0].geometry, {
                        style: {
                            color: '#3B82F6',
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
                    
                    console.log('Route drawn successfully');
                } catch (error) {
                    console.error('Error drawing route:', error);
                    this.drawStraightLine(admin);
                }
            } else {
                console.warn('Route data invalid or empty, falling back to straight line', routeData);
                // Fallback to straight line if routing fails
                this.drawStraightLine(admin);
            }
        } catch (error) {
            console.error('Error fetching route:', error);
            // Fallback to straight line
            this.drawStraightLine(admin);
        }

        // Update route info panel
        this.updateRouteInfo(admin);

        // Highlight selected admin
        this.highlightSelectedAdmin(adminId);

        // Open popup for selected admin
        this.markers.forEach(marker => {
            const markerLatLng = marker.getLatLng();
            if (markerLatLng.lat === admin.latitude && markerLatLng.lng === admin.longitude) {
                marker.openPopup();
            }
        });
    }

    updateRouteInfo(admin) {
        const routeInfo = document.getElementById('route-info');
        if (!routeInfo) return;

        // Show the panel
        routeInfo.classList.remove('hidden');

        // Update the information
        document.getElementById('distance').textContent = admin.distance_km ? `${admin.distance_km} km` : '-';
        document.getElementById('travel-time').textContent = admin.eta_minutes ? `${admin.eta_minutes} min` : '-';
        document.getElementById('eta').textContent = admin.eta || '-';
        document.getElementById('shop-name').textContent = admin.name || admin.branch_name || '-';
        document.getElementById('shop-address').textContent = admin.address || '-';
        document.getElementById('shop-phone').textContent = admin.phone || '-';

        // Update Google Maps link
        const googleMapsLink = document.getElementById('google-maps-link');
        if (googleMapsLink && this.userLocation) {
            const url = `https://www.google.com/maps/dir/?api=1&origin=${this.userLocation.latitude},${this.userLocation.longitude}&destination=${admin.latitude},${admin.longitude}`;
            googleMapsLink.href = url;
        }
    }

    async fetchRoute(startLat, startLng, endLat, endLng) {
        if (!this.apiKey) {
            console.error('Geoapify API key is missing');
            return null;
        }

        try {
            const url = `https://api.geoapify.com/v1/routing?waypoints=${startLat},${startLng}|${endLat},${endLng}&mode=drive&apiKey=${this.apiKey}`;
            console.log('Fetching route from Geoapify API...');
            console.log('Start:', startLat, startLng);
            console.log('End:', endLat, endLng);
            
            const response = await fetch(url);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('Routing API error:', response.status, errorText);
                throw new Error(`Routing API request failed: ${response.status} - ${errorText}`);
            }
            
            const data = await response.json();
            console.log('Route data received successfully:', data);
            
            // Check if route data is valid
            if (!data.features || data.features.length === 0) {
                console.error('No route features in response:', data);
                return null;
            }
            
            if (!data.features[0].geometry) {
                console.error('No geometry in route feature:', data.features[0]);
                return null;
            }
            
            console.log('Route geometry type:', data.features[0].geometry.type);
            console.log('Route coordinates count:', data.features[0].geometry.coordinates?.length);
            
            return data;
        } catch (error) {
            console.error('Error fetching route from Geoapify:', error);
            return null;
        }
    }

    drawStraightLine(admin) {
        console.warn('Drawing straight line fallback (routing API failed or unavailable)');
        this.routeLayer = L.polyline([
            [this.userLocation.latitude, this.userLocation.longitude],
            [admin.latitude, admin.longitude]
        ], {
            color: '#EF4444',
            weight: 4,
            opacity: 0.7,
            dashArray: '10, 10'
        }).addTo(this.map);

        const bounds = L.latLngBounds([
            [this.userLocation.latitude, this.userLocation.longitude],
            [admin.latitude, admin.longitude]
        ]);
        this.map.fitBounds(bounds, { padding: [80, 80] });
    }

    highlightSelectedAdmin(adminId) {
        // Remove previous highlights
        document.querySelectorAll('.admin-item').forEach(item => {
            item.classList.remove('border-primary-500', 'bg-primary-50');
            item.classList.add('border-gray-200');
        });

        // Highlight selected admin
        const selectedItem = document.querySelector(`[data-admin-id="${adminId}"]`);
        if (selectedItem) {
            selectedItem.classList.remove('border-gray-200');
            selectedItem.classList.add('border-primary-500', 'bg-primary-50');
        }
    }

    focusAdmin(lat, lng) {
        if (this.map) {
            this.map.setView([lat, lng], 16);
        }
    }

    showError(message) {
        const listContainer = document.getElementById('admin-list');
        if (listContainer) {
            listContainer.innerHTML = `<p class="text-red-500">${message}</p>`;
        }
    }

    startAutoRefresh() {
        this.intervalId = setInterval(() => {
            this.loadAdminLocations();
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
