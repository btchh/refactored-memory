/**
 * Route Planner
 * Plans and displays routes from admin to user locations
 */

export class RoutePlanner {
    constructor(options = {}) {
        this.mapId = options.mapId || 'map';
        this.apiKey = options.apiKey || '';
        this.usersUrl = options.usersUrl || '/admin/api/users';
        this.routeUrl = options.routeUrl || '/admin/get-route';
        this.map = null;
        this.routeLayer = null;
        this.markers = [];
        
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

        // Load users
        this.loadUsers();
    }

    async loadUsers() {
        try {
            const response = await fetch(this.usersUrl);
            const data = await response.json();
            
            if (data.success) {
                this.displayUsers(data.users);
            } else {
                this.showError('user-list', 'Failed to load users');
            }
        } catch (error) {
            console.error('Error loading users:', error);
            this.showError('user-list', 'Failed to load users');
        }
    }

    displayUsers(users) {
        const listContainer = document.getElementById('user-list');
        if (!listContainer) return;
        
        if (users.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500">No users found</p>';
            return;
        }

        // Sort by distance if available
        if (users[0]?.distance_km !== undefined) {
            users.sort((a, b) => a.distance_km - b.distance_km);
        }

        listContainer.innerHTML = users.map(user => `
            <button 
                onclick="window.routePlanner.selectUser(${user.id})"
                class="user-item w-full text-left p-4 border border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 group"
                data-user-id="${user.id}"
            >
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-red-200 group-hover:shadow-red-300 transition-shadow">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate group-hover:text-indigo-700 transition-colors">${user.name}</h3>
                        <p class="text-sm text-gray-600 truncate">${user.phone}</p>
                        ${user.address ? `
                            <p class="text-xs text-gray-500 truncate mt-1">${user.address}</p>
                        ` : ''}
                        ${user.distance_km !== undefined ? `
                            <div class="mt-2 flex gap-2 text-xs">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg font-semibold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    ${user.distance_km} km
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-lg font-semibold">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    ${user.eta_minutes} min
                                </span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </button>
        `).join('');
    }

    async selectUser(userId) {
        try {
            const response = await fetch(`${this.routeUrl}/${userId}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayRoute(data);
            } else {
                window.Toast?.error(data.message || 'Failed to calculate route');
            }
        } catch (error) {
            console.error('Error getting route:', error);
            window.Toast?.error('Failed to calculate route: ' + error.message);
        }
    }

    displayRoute(data) {
        // Clear ALL existing route layers (in case there are multiple)
        this.map.eachLayer((layer) => {
            if (layer instanceof L.Polyline || layer instanceof L.GeoJSON) {
                // Don't remove the base tile layer
                if (!(layer instanceof L.TileLayer)) {
                    this.map.removeLayer(layer);
                }
            }
        });
        
        // Clear existing markers and route
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
            this.routeLayer = null;
        }

        // Add admin marker (start) - Your location
        const adminMarker = L.marker([data.admin.latitude, data.admin.longitude], {
            icon: L.divIcon({
                className: 'custom-marker-shop',
                html: `
                    <div style="position: relative;">
                        <div style="
                            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
                            width: 40px;
                            height: 40px;
                            border-radius: 50% 50% 50% 0;
                            transform: rotate(-45deg);
                            border: 4px solid white;
                            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
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
                            background: rgba(99, 102, 241, 0.3);
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
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Your Location</h3>
                        <p class="text-xs text-gray-500">${data.admin.name || 'Branch'}</p>
                    </div>
                </div>
            </div>
        `, {
            maxWidth: 300,
            className: 'custom-popup'
        });
        this.markers.push(adminMarker);

        // Add user marker (destination)
        const userMarker = L.marker([data.user.latitude, data.user.longitude], {
            icon: L.divIcon({
                className: 'custom-marker-user',
                html: `
                    <div style="position: relative;">
                        <div style="
                            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
                            width: 40px;
                            height: 40px;
                            border-radius: 50% 50% 50% 0;
                            transform: rotate(-45deg);
                            border: 4px solid white;
                            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
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
                            background: rgba(239, 68, 68, 0.3);
                            border-radius: 50%;
                        "></div>
                    </div>
                `,
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            }),
            zIndexOffset: 500
        }).addTo(this.map).bindPopup(`
            <div class="p-3">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Customer</h3>
                        <p class="text-xs text-gray-500">${data.user.name}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2">${data.user.address}</p>
            </div>
        `, {
            maxWidth: 300,
            className: 'custom-popup'
        });
        this.markers.push(userMarker);

        // Draw route line (straight line between points)
        this.routeLayer = L.polyline([
            [data.admin.latitude, data.admin.longitude],
            [data.user.latitude, data.user.longitude]
        ], {
            color: '#6366F1',
            weight: 6,
            opacity: 0.9,
            lineJoin: 'round',
            lineCap: 'round'
        }).addTo(this.map);

        // Fit map to show entire route
        const bounds = L.latLngBounds([
            [data.admin.latitude, data.admin.longitude],
            [data.user.latitude, data.user.longitude]
        ]);
        this.map.fitBounds(bounds, { padding: [80, 80] });

        // Update route info
        this.updateRouteInfo(data);
    }

    updateRouteInfo(data) {
        const routeInfo = document.getElementById('route-info');
        if (!routeInfo) return;

        routeInfo.classList.remove('hidden');
        
        const distance = document.getElementById('distance');
        const travelTime = document.getElementById('travel-time');
        const eta = document.getElementById('eta');
        const destName = document.getElementById('destination-name');
        const destAddress = document.getElementById('destination-address');

        if (distance) distance.textContent = data.distance_km + ' km';
        if (travelTime) travelTime.textContent = data.eta_minutes + ' min';
        if (eta) eta.textContent = data.eta;
        if (destName) destName.textContent = data.user.name;
        if (destAddress) destAddress.textContent = data.user.address;
    }

    showError(elementId, message) {
        const element = document.getElementById(elementId);
        if (element) {
            element.innerHTML = `<p class="text-red-500">${message}</p>`;
        }
    }

    destroy() {
        if (this.map) {
            this.map.remove();
        }
    }
}
