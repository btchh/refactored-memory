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
                    className: 'custom-marker-user',
                    html: `
                        <div style="position: relative;">
                            <div style="
                                background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                                width: 40px;
                                height: 40px;
                                border-radius: 50% 50% 50% 0;
                                transform: rotate(-45deg);
                                border: 4px solid white;
                                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <svg style="transform: rotate(45deg); width: 20px; height: 20px; color: white;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div style="
                                position: absolute;
                                bottom: -8px;
                                left: 50%;
                                transform: translateX(-50%);
                                width: 12px;
                                height: 12px;
                                background: rgba(16, 185, 129, 0.3);
                                border-radius: 50%;
                                animation: pulse 2s infinite;
                            "></div>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                }),
                zIndexOffset: 1000
            }).addTo(this.map).bindPopup(`
                <div class="p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Your Location</h3>
                            <p class="text-xs text-gray-500">${userLoc.name || 'Current Position'}</p>
                        </div>
                    </div>
                </div>
            `, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            this.markers.push(userMarker);
        }

        if (admins.length === 0) {
            return;
        }

        // Add markers for each admin
        admins.forEach(admin => {
            const popupContent = `
                <div class="p-3">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">${admin.branch_name}</h3>
                        </div>
                    </div>
                    ${admin.distance_km !== undefined ? `
                        <div class="grid grid-cols-3 gap-2 mt-2">
                            <div class="bg-blue-50 rounded-lg p-2 text-center">
                                <p class="text-xs text-blue-600 font-medium">Distance</p>
                                <p class="text-sm font-bold text-blue-700">${admin.distance_km} km</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-2 text-center">
                                <p class="text-xs text-green-600 font-medium">Time</p>
                                <p class="text-sm font-bold text-green-700">${admin.eta_minutes} min</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-2 text-center">
                                <p class="text-xs text-purple-600 font-medium">ETA</p>
                                <p class="text-sm font-bold text-purple-700">${admin.eta}</p>
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            
            const marker = L.marker([admin.latitude, admin.longitude], {
                icon: L.divIcon({
                    className: 'custom-marker-shop',
                    html: `
                        <div style="position: relative;">
                            <div style="
                                background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
                                width: 40px;
                                height: 40px;
                                border-radius: 50% 50% 50% 0;
                                transform: rotate(-45deg);
                                border: 4px solid white;
                                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <svg style="transform: rotate(45deg); width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div style="
                                position: absolute;
                                bottom: -8px;
                                left: 50%;
                                transform: translateX(-50%);
                                width: 12px;
                                height: 12px;
                                background: rgba(59, 130, 246, 0.3);
                                border-radius: 50%;
                            "></div>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                }),
                zIndexOffset: 500
            }).addTo(this.map).bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
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
                class="admin-item w-full text-left p-4 border border-gray-200 rounded-xl hover:border-teal-400 hover:bg-teal-50 transition-all duration-200 group"
                data-admin-id="${admin.id}"
            >
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-200 group-hover:shadow-blue-300 transition-shadow">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate group-hover:text-teal-700 transition-colors">${admin.branch_name}</h3>
                        ${admin.distance_km !== undefined ? `
                            <div class="mt-2 flex gap-2 text-xs">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-teal-100 text-teal-700 rounded-lg font-semibold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    ${admin.distance_km} km
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-lg font-semibold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    ${admin.eta_minutes} min
                                </span>
                            </div>
                        ` : ''}
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

        // Clear ALL existing route layers (in case there are multiple)
        this.map.eachLayer((layer) => {
            if (layer instanceof L.Polyline || layer instanceof L.GeoJSON) {
                // Don't remove the base tile layer
                if (!(layer instanceof L.TileLayer)) {
                    // Check if it's not a marker
                    if (!this.markers.includes(layer)) {
                        this.map.removeLayer(layer);
                    }
                }
            }
        });
        
        // Clear the route layer reference
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
            this.routeLayer = null;
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
        document.getElementById('shop-name').textContent = admin.branch_name || '-';

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
        
        // Ensure any existing route is removed first
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
            this.routeLayer = null;
        }
        
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
