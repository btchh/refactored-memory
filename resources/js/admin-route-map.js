/**
 * Admin Route to User Map Module
 * Handles route calculation and display for admin portal
 */

import {
    showNotification,
    initializeMap,
    fetchWithRetry,
    createLoadingSpinner,
    createErrorDisplay,
    createMarkerIcon,
    clearMapLayers
} from './map-utils.js';

class AdminRouteMap {
    constructor(apiKey) {
        this.map = null;
        this.routeLayer = null;
        this.markers = [];
        this.apiKey = apiKey;
        this.defaultCenter = [14.5995, 120.9842];
        this.defaultZoom = 13;
    }

    /**
     * Initialize the map and load users
     */
    init() {
        this.map = initializeMap('map', this.defaultCenter, this.defaultZoom, this.apiKey);
        if (this.map) {
            this.loadUsers();
        }
    }

    /**
     * Load users list
     */
    async loadUsers() {
        const listContainer = document.getElementById('user-list');
        listContainer.innerHTML = createLoadingSpinner('Loading users...');

        try {
            const data = await fetchWithRetry('/api/users', {}, 2, (retryCount, maxRetries) => {
                listContainer.innerHTML = createLoadingSpinner(`Connection failed. Retrying (${retryCount}/${maxRetries})...`);
            });

            console.log('Users data received:', data);

            if (data.success && data.data && data.data.users) {
                this.displayUsers(data.data.users);
            } else if (data.success && data.users) {
                // Fallback for different response structure
                this.displayUsers(data.users);
            } else {
                console.warn('Unexpected response structure:', data);
                listContainer.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="mt-2 text-gray-500">No users found</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading users:', error);
            listContainer.innerHTML = createErrorDisplay(
                'Failed to load users',
                error.message || 'Network error occurred',
                'window.adminRouteMap.loadUsers()'
            );
        }
    }

    /**
     * Display users in the list
     * @param {Array} users - Array of user objects
     */
    displayUsers(users) {
        const listContainer = document.getElementById('user-list');

        if (users.length === 0) {
            listContainer.innerHTML = '<p class="text-gray-500">No users found</p>';
            return;
        }

        listContainer.innerHTML = users.map(user => `
            <div class="p-4 bg-gray-50 rounded-lg hover:bg-blue-50 cursor-pointer transition" 
                 onclick="window.adminRouteMap.selectUser(${user.id})">
                <h3 class="font-semibold">${user.name}</h3>
                <p class="text-sm text-gray-600">${user.phone}</p>
                <p class="text-xs text-gray-500">${user.address || 'No address'}</p>
            </div>
        `).join('');
    }

    /**
     * Select user and calculate route
     * @param {number} userId - User ID
     */
    async selectUser(userId) {
        console.log('Selected user:', userId);

        const routeInfo = document.getElementById('route-info');
        routeInfo.classList.remove('hidden');
        routeInfo.innerHTML = createLoadingSpinner('Calculating route...');

        try {
            const response = await fetch(`/admin/get-route/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('Route data:', data);

            if (data.success && data.data) {
                this.displayRoute(data.data);
            } else {
                this.showRouteError(data.message || 'Failed to calculate route');
            }
        } catch (error) {
            console.error('Error getting route:', error);
            this.showRouteError(error.message || 'Network error occurred while calculating route');
        }
    }

    /**
     * Show route error
     * @param {string} message - Error message
     */
    showRouteError(message) {
        const routeInfo = document.getElementById('route-info');
        routeInfo.classList.remove('hidden');
        routeInfo.innerHTML = `
            <div class="text-center py-6">
                <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="mt-2 text-red-600 font-semibold">Route Calculation Failed</p>
                <p class="mt-1 text-sm text-gray-600">${message}</p>
                <button onclick="this.closest('#route-info').classList.add('hidden')" class="mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm">
                    Close
                </button>
            </div>
        `;
    }

    /**
     * Display route on map
     * @param {Object} data - Route data
     */
    displayRoute(data) {
        try {
            // Validate data
            if (!data.admin || !data.user || !data.route) {
                throw new Error('Invalid route data received');
            }

            if (!data.admin.latitude || !data.admin.longitude || !data.user.latitude || !data.user.longitude) {
                throw new Error('Missing location coordinates');
            }

            // Clear existing markers and route
            clearMapLayers(this.map, this.markers, this.routeLayer ? [this.routeLayer] : []);
            this.routeLayer = null;

            // Add admin marker (start)
            const adminMarker = L.marker([data.admin.latitude, data.admin.longitude], {
                icon: createMarkerIcon('#3B82F6')
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
                icon: createMarkerIcon('#EF4444')
            }).addTo(this.map).bindPopup(`
                <div class="p-2">
                    <h3 class="font-bold">Destination</h3>
                    <p class="text-sm">${data.user.name}</p>
                    <p class="text-xs text-gray-500">${data.user.address}</p>
                </div>
            `);
            this.markers.push(userMarker);

            // Draw route
            if (data.route.geometry) {
                this.routeLayer = L.geoJSON(data.route.geometry, {
                    style: {
                        color: '#3B82F6',
                        weight: 5,
                        opacity: 0.7
                    }
                }).addTo(this.map);

                // Fit map to show entire route
                const bounds = this.routeLayer.getBounds();
                this.map.fitBounds(bounds, { padding: [50, 50] });
            } else {
                // If no geometry, just fit to markers
                const group = L.featureGroup(this.markers);
                this.map.fitBounds(group.getBounds().pad(0.1));
            }

            // Update route info
            const routeInfo = document.getElementById('route-info');
            routeInfo.classList.remove('hidden');
            routeInfo.innerHTML = `
                <h2 class="text-xl font-semibold mb-4">Route Information</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Distance</p>
                        <p class="text-2xl font-bold text-blue-600">${data.route.distance_km} km</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Travel Time</p>
                        <p class="text-2xl font-bold text-green-600">${data.route.time_minutes} min</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estimated Arrival</p>
                        <p class="text-2xl font-bold text-purple-600">${data.route.eta}</p>
                        ${data.route.current_time ? `<p class="text-xs text-gray-400 mt-1">Current time: ${data.route.current_time}</p>` : ''}
                    </div>
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600">Destination</p>
                        <p class="font-semibold">${data.user.name}</p>
                        <p class="text-sm text-gray-500">${data.user.address}</p>
                    </div>
                    ${data.route.method === 'distance' ? `
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs text-yellow-800">
                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Showing straight-line distance (routing service unavailable)
                            </p>
                        </div>
                    ` : ''}
                </div>
            `;

            showNotification('Route calculated successfully', 'success');
        } catch (error) {
            console.error('Error displaying route:', error);
            this.showRouteError(error.message || 'Failed to display route on map');
        }
    }
}

// Initialize on DOM ready (only if on admin route page)
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the admin route page by looking for the map element
    const mapElement = document.getElementById('map');
    const userListElement = document.getElementById('user-list');
    
    // Only initialize if both map and user-list elements exist (admin route page)
    if (mapElement && userListElement) {
        const apiKey = document.querySelector('[data-geoapify-key]')?.dataset.geoapifyKey;
        if (apiKey) {
            window.adminRouteMap = new AdminRouteMap(apiKey);
            window.adminRouteMap.init();
        } else {
            console.error('Geoapify API key not found for admin route map');
        }
    }
});
