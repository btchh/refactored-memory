/**
 * User Location Tracker (for Admin)
 * Tracks and displays user locations on a map with routing
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
                    className: 'custom-marker-admin',
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
                                animation: pulse 2s infinite;
                            "></div>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                }),
                zIndexOffset: 1000
            }).addTo(this.map);
            this.markers.push(adminMarker);
        }

        if (users.length === 0) {
            return;
        }

        // Add markers for each user
        users.forEach(user => {
            const marker = L.marker([user.latitude, user.longitude], {
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
                            "></div>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                }),
                zIndexOffset: 500
            }).addTo(this.map);
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
            listContainer.innerHTML = `
                <div class="rounded-xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No Customers Found</h3>
                    <p class="text-gray-500">No customers have booked with your branch yet</p>
                </div>
            `;
            this.updateStatCards(0, 0, 0, 0);
            return;
        }

        // Sort by distance if available
        if (users[0].distance_km !== undefined) {
            users.sort((a, b) => a.distance_km - b.distance_km);
        }

        // Store users for later use
        this.users = users;

        // Update stat cards
        this.updateStatCards(
            users.length,
            users.length,
            this.calculateAverageDistance(users),
            this.calculateAverageETA(users)
        );

        listContainer.innerHTML = users.map((user, index) => `
            <button 
                onclick="window.userTracker.selectUser(${user.id})"
                class="user-item group w-full text-left bg-white rounded-xl border-2 border-gray-200 hover:border-wash hover:shadow-lg transition-all"
                data-user-id="${user.id}"
            >
                <div class="flex items-center gap-3 p-3">
                    <!-- Customer Number Badge -->
                    <div class="w-10 h-10 bg-gradient-to-br from-wash to-wash-dark rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-md flex-shrink-0">
                        ${index + 1}
                    </div>
                    
                    <!-- Customer Name -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-gray-900 group-hover:text-wash transition-colors truncate">${user.name}</h3>
                            <span class="badge badge-in-progress text-xs flex-shrink-0">Active</span>
                        </div>
                    </div>
                    
                    <!-- Arrow Icon -->
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-wash transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </div>
            </button>
        `).join('');
    }

    updateStatCards(activeDeliveries, totalCustomers, avgDistance, avgETA) {
        // Update Active Deliveries
        const statActive = document.getElementById('stat-active');
        if (statActive) {
            statActive.textContent = activeDeliveries;
        }

        // Update Total Customers
        const statCustomers = document.getElementById('stat-customers');
        if (statCustomers) {
            statCustomers.textContent = totalCustomers;
        }

        // Update Average Distance
        const statDistance = document.getElementById('stat-distance');
        if (statDistance) {
            statDistance.textContent = avgDistance > 0 ? avgDistance : '0';
        }

        // Update Average ETA
        const statEta = document.getElementById('stat-eta');
        if (statEta) {
            statEta.textContent = avgETA > 0 ? avgETA : '0';
        }
    }

    calculateAverageDistance(users) {
        if (users.length === 0) return 0;
        const total = users.reduce((sum, user) => sum + (user.distance_km || 0), 0);
        return Math.round(total / users.length * 10) / 10; // Round to 1 decimal
    }

    calculateAverageETA(users) {
        if (users.length === 0) return 0;
        const total = users.reduce((sum, user) => sum + (user.eta_minutes || 0), 0);
        return Math.round(total / users.length);
    }

    async selectUser(userId) {
        const user = this.users.find(u => u.id === userId);
        if (!user || !this.adminLocation) {
            console.error('User or admin location not found', { userId, user, adminLocation: this.adminLocation });
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
            console.log('Fetching route for user:', user.name);
            const routeData = await this.fetchRoute(
                this.adminLocation.latitude,
                this.adminLocation.longitude,
                user.latitude,
                user.longitude
            );

            if (routeData && routeData.features && routeData.features.length > 0 && routeData.features[0].geometry) {
                console.log('Drawing route from API data', routeData.features[0].geometry);
                
                try {
                    // Draw the actual route using GeoJSON
                    this.routeLayer = L.geoJSON(routeData.features[0].geometry, {
                        style: {
                            color: '#10B981',
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
                    this.drawStraightLine(user);
                }
            } else {
                console.warn('Route data invalid or empty, falling back to straight line', routeData);
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
    }

    updateRouteInfo(user) {
        const routeInfo = document.getElementById('route-info');
        if (!routeInfo) return;

        // Show the panel (use flex to match the layout)
        routeInfo.classList.remove('hidden');
        routeInfo.classList.add('flex');

        // Update the information
        document.getElementById('distance').textContent = user.distance_km ? `${user.distance_km} km` : '-';
        document.getElementById('travel-time').textContent = user.eta_minutes ? `${user.eta_minutes} min` : '-';
        document.getElementById('eta').textContent = user.eta || '-';
        document.getElementById('customer-name').textContent = user.name;
        document.getElementById('customer-address').textContent = user.address || '-';

        // Update Google Maps link
        const googleMapsLink = document.getElementById('google-maps-link');
        if (googleMapsLink && this.adminLocation) {
            const url = `https://www.google.com/maps/dir/?api=1&origin=${this.adminLocation.latitude},${this.adminLocation.longitude}&destination=${user.latitude},${user.longitude}`;
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

    drawStraightLine(user) {
        console.warn('Drawing straight line fallback (routing API failed or unavailable)');
        
        // Ensure any existing route is removed first
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
            this.routeLayer = null;
        }
        
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
            item.classList.remove('border-wash', 'bg-wash/5', 'shadow-lg');
            item.classList.add('border-gray-200');
        });

        // Highlight selected user
        const selectedItem = document.querySelector(`[data-user-id="${userId}"]`);
        if (selectedItem) {
            selectedItem.classList.remove('border-gray-200');
            selectedItem.classList.add('border-wash', 'bg-wash/5', 'shadow-lg');
            
            // Scroll into view
            selectedItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
            listContainer.innerHTML = `<p class="text-red-500 text-center py-8">${message}</p>`;
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
