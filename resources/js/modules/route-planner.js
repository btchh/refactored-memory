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

        listContainer.innerHTML = users.map(user => `
            <div class="p-4 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer transition" 
                 onclick="window.routePlanner.selectUser(${user.id})">
                <h3 class="font-semibold">${user.name}</h3>
                <p class="text-sm text-gray-600">${user.phone}</p>
                <p class="text-xs text-gray-500">${user.address || 'No address'}</p>
                ${user.distance_km ? `
                    <div class="mt-2 pt-2 border-t border-gray-200">
                        <p class="text-xs text-blue-600 font-medium">
                            📍 ${user.distance_km} km • ⏱️ ${user.eta_minutes} min • 🕐 ETA: ${user.eta}
                        </p>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    async selectUser(userId) {
        try {
            const response = await fetch(`${this.routeUrl}/${userId}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayRoute(data);
            } else {
                alert(data.message || 'Failed to calculate route');
            }
        } catch (error) {
            console.error('Error getting route:', error);
            alert('Failed to calculate route: ' + error.message);
        }
    }

    displayRoute(data) {
        // Clear existing markers and route
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
        if (this.routeLayer) {
            this.map.removeLayer(this.routeLayer);
        }

        // Add admin marker (start)
        const adminMarker = L.marker([data.admin.latitude, data.admin.longitude], {
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color: #3B82F6; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                iconSize: [30, 30]
            })
        }).addTo(this.map).bindPopup(`
            <div class="p-2">
                <h3 class="font-bold">Your Location</h3>
                <p class="text-sm">${data.admin.name}</p>
                <p class="text-xs text-gray-500">${data.admin.address}</p>
            </div>
        `);
        this.markers.push(adminMarker);

        // Add user marker (destination)
        const userMarker = L.marker([data.user.latitude, data.user.longitude], {
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<div style="background-color: #EF4444; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
                iconSize: [30, 30]
            })
        }).addTo(this.map).bindPopup(`
            <div class="p-2">
                <h3 class="font-bold">Destination</h3>
                <p class="text-sm">${data.user.name}</p>
                <p class="text-xs text-gray-500">${data.user.address}</p>
            </div>
        `);
        this.markers.push(userMarker);

        // Draw route line (straight line between points)
        this.routeLayer = L.polyline([
            [data.admin.latitude, data.admin.longitude],
            [data.user.latitude, data.user.longitude]
        ], {
            color: '#3B82F6',
            weight: 5,
            opacity: 0.7,
            dashArray: '10, 10'
        }).addTo(this.map);

        // Fit map to show entire route
        const bounds = L.latLngBounds([
            [data.admin.latitude, data.admin.longitude],
            [data.user.latitude, data.user.longitude]
        ]);
        this.map.fitBounds(bounds, { padding: [50, 50] });

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
