/**
 * User Track Admin Map Module
 * Handles admin location tracking and display for user portal
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

class UserTrackAdminMap {
    constructor(apiKey, userInfo) {
        this.map = null;
        this.markers = [];
        this.routeLayers = [];
        this.userLocation = null;
        this.apiKey = apiKey;
        this.userInfo = userInfo;
        this.defaultCenter = [14.5995, 120.9842];
        this.defaultZoom = 13;
        this.autoRefreshInterval = null;
    }

    /**
     * Initialize the map and load admin locations
     */
    init() {
        this.map = initializeMap('map', this.defaultCenter, this.defaultZoom, this.apiKey);
        if (this.map) {
            this.loadAdminLocations();
            this.startAutoRefresh();
        }
    }

    /**
     * Start auto-refresh every 30 seconds
     */
    startAutoRefresh() {
        this.autoRefreshInterval = setInterval(() => {
            this.loadAdminLocations(false);
        }, 30000);
    }

    /**
     * Stop auto-refresh
     */
    stopAutoRefresh() {
        if (this.autoRefreshInterval) {
            clearInterval(this.autoRefreshInterval);
            this.autoRefreshInterval = null;
        }
    }

    /**
     * Load admin locations
     * @param {boolean} forceGeocode - Force geocoding of addresses
     */
    async loadAdminLocations(forceGeocode = false) {
        const listContainer = document.getElementById('admin-list');
        const url = forceGeocode 
            ? `${this.userInfo.adminLocationRoute}?force_geocode=1`
            : this.userInfo.adminLocationRoute;

        if (!forceGeocode) {
            listContainer.innerHTML = createLoadingSpinner('Loading admin locations...');
        }

        try {
            const data = await fetchWithRetry(url, {}, 2, (retryCount, maxRetries) => {
                listContainer.innerHTML = createLoadingSpinner(`Connection failed. Retrying (${retryCount}/${maxRetries})...`);
            });

            console.log('Data received:', data);

            if (data.success && data.data) {
                const admins = data.data.admins || [];
                const userLoc = data.data.user_location || null;

                console.log('Number of admins:', admins.length);
                this.userLocation = userLoc;
                this.updateMap(admins, this.userLocation);
                this.updateAdminList(admins);

                if (forceGeocode) {
                    showNotification('Locations refreshed successfully', 'success');
                }

                // Show warning if user location is missing
                if (!this.userLocation && admins.length > 0) {
                    showNotification('Your location is not set. Distance calculations are unavailable.', 'warning', 8000);
                }
            } else {
                throw new Error(data.message || 'Failed to load admin locations');
            }
        } catch (error) {
            console.error('Error loading admin locations:', error);
            listContainer.innerHTML = createErrorDisplay(
                'Failed to load admin locations',
                error.message || 'Network error occurred',
                'window.userTrackAdminMap.loadAdminLocations()'
            );
            showNotification(error.message || 'Failed to load admin locations', 'error', 0);
        }
    }

    /**
     * Refresh locations with geocoding
     */
    async refreshLocations() {
        const btn = document.getElementById('refresh-btn');
        const originalContent = btn.innerHTML;

        // Show loading state
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span>Refreshing...</span>
        `;

        await this.loadAdminLocations(true);

        // Reset button after 3 seconds
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }, 3000);
    }

    /**
     * Update map with markers
     * @param {Array} admins - Array of admin objects
     * @param {Object|null} userLoc - User location object
     */
    updateMap(admins, userLoc) {
        try {
            // Clear existing markers and routes
            clearMapLayers(this.map, this.markers, this.routeLayers);

            // Add user location marker if available
            if (userLoc && userLoc.latitude && userLoc.longitude) {
                try {
                    const userMarker = L.marker([userLoc.latitude, userLoc.longitude], {
                        icon: createMarkerIcon('#10B981')
                    }).addTo(this.map).bindPopup(`
                        <div class="p-2">
                            <h3 class="font-bold">Your Location</h3>
                            <p class="text-sm">${this.userInfo.name}</p>
                            <p class="text-xs text-gray-500">${this.userInfo.address}</p>
                        </div>
                    `);
                    this.markers.push(userMarker);
                } catch (error) {
                    console.error('Error adding user marker:', error);
                    showNotification('Failed to display your location on map', 'warning');
                }
            }

            if (admins.length === 0) {
                showNotification('No admin locations available to display', 'info');
                return;
            }

            // Add markers for each admin
            let successCount = 0;
            let failCount = 0;

            admins.forEach(admin => {
                try {
                    // Validate coordinates
                    if (!admin.latitude || !admin.longitude || 
                        isNaN(admin.latitude) || isNaN(admin.longitude)) {
                        console.warn('Invalid coordinates for admin:', admin.name);
                        failCount++;
                        return;
                    }

                    const popupContent = `
                        <div class="p-2">
                            <h3 class="font-bold">${admin.name}</h3>
                            <p class="text-sm">${admin.phone}</p>
                            ${admin.distance_km !== undefined ? `
                                <div class="mt-2 pt-2 border-t space-y-1">
                                    <div>
                                        <p class="text-xs text-gray-600">Distance</p>
                                        <p class="text-sm font-semibold text-blue-600">${admin.distance_km} km</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Travel Time</p>
                                        <p class="text-sm font-semibold text-green-600">${admin.eta_minutes} min</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-600">Estimated Arrival</p>
                                        <p class="text-sm font-semibold text-purple-600">${admin.eta}</p>
                                        ${admin.current_time ? `<p class="text-xs text-gray-400 mt-1">Current time: ${admin.current_time}</p>` : ''}
                                    </div>
                                </div>
                            ` : `
                                <div class="mt-2 pt-2 border-t">
                                    <p class="text-xs text-gray-500">Distance calculation unavailable</p>
                                </div>
                            `}
                            <p class="text-xs text-gray-500 mt-2">Updated: ${admin.updated_at}</p>
                        </div>
                    `;

                    const marker = L.marker([admin.latitude, admin.longitude], {
                        icon: createMarkerIcon('#3B82F6')
                    }).addTo(this.map).bindPopup(popupContent);
                    this.markers.push(marker);
                    successCount++;

                    // Draw route line from user to admin if user location is available
                    if (userLoc && userLoc.latitude && userLoc.longitude && admin.distance_km !== undefined) {
                        try {
                            const routeLine = L.polyline([
                                [userLoc.latitude, userLoc.longitude],
                                [admin.latitude, admin.longitude]
                            ], {
                                color: '#3B82F6',
                                weight: 3,
                                opacity: 0.6,
                                dashArray: '5, 10'
                            }).addTo(this.map);
                            this.routeLayers.push(routeLine);
                        } catch (error) {
                            console.error('Error drawing route line:', error);
                        }
                    }
                } catch (error) {
                    console.error('Error adding admin marker:', admin.name, error);
                    failCount++;
                }
            });

            // Show notification if some admins failed to display
            if (failCount > 0) {
                showNotification(`${failCount} admin location(s) could not be displayed due to invalid coordinates`, 'warning');
            }

            // Fit map to show all markers
            if (this.markers.length > 0) {
                try {
                    const group = L.featureGroup(this.markers);
                    this.map.fitBounds(group.getBounds().pad(0.1));
                } catch (error) {
                    console.error('Error fitting map bounds:', error);
                    // Fallback to default view
                    this.map.setView(this.defaultCenter, this.defaultZoom);
                }
            } else {
                showNotification('No valid admin locations to display on map', 'warning');
            }
        } catch (error) {
            console.error('Error updating map:', error);
            showNotification('Failed to update map display', 'error');
        }
    }

    /**
     * Update admin list
     * @param {Array} admins - Array of admin objects
     */
    updateAdminList(admins) {
        const listContainer = document.getElementById('admin-list');

        if (admins.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="mt-2 text-gray-500">No admin locations available</p>
                    <p class="mt-1 text-sm text-gray-400">Try refreshing to geocode addresses</p>
                </div>
            `;
            return;
        }

        try {
            // Sort by distance if available
            const hasDistance = admins.some(admin => admin.distance_km !== undefined);
            if (hasDistance) {
                admins.sort((a, b) => {
                    // Put admins without distance at the end
                    if (a.distance_km === undefined) return 1;
                    if (b.distance_km === undefined) return -1;
                    return a.distance_km - b.distance_km;
                });
            }

            listContainer.innerHTML = admins.map(admin => {
                // Validate admin data
                if (!admin.name || !admin.latitude || !admin.longitude) {
                    console.warn('Invalid admin data:', admin);
                    return '';
                }

                return `
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer transition" 
                         onclick="window.userTrackAdminMap.focusAdmin(${admin.latitude}, ${admin.longitude})">
                        <div class="flex-1">
                            <h3 class="font-semibold">${admin.name}</h3>
                            <p class="text-sm text-gray-600">${admin.phone || 'No phone'}</p>
                            ${admin.distance_km !== undefined ? `
                                <div class="mt-2 flex flex-wrap gap-3 text-xs">
                                    <span class="text-blue-600 font-semibold">📍 ${admin.distance_km} km</span>
                                    <span class="text-green-600 font-semibold">⏱️ ${admin.eta_minutes} min</span>
                                    <span class="text-purple-600 font-semibold">🕐 ETA: ${admin.eta}</span>
                                </div>
                                ${admin.current_time ? `
                                    <div class="mt-1 text-xs text-gray-400">
                                        Current time: ${admin.current_time}
                                    </div>
                                ` : ''}
                            ` : `
                                <div class="mt-2 text-xs text-gray-400">
                                    Distance calculation unavailable
                                </div>
                            `}
                            <p class="text-xs text-gray-500 mt-1">Updated: ${admin.updated_at || 'Never'}</p>
                        </div>
                        <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                `;
            }).filter(html => html !== '').join('');

            // If all admins were filtered out
            if (listContainer.innerHTML.trim() === '') {
                listContainer.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="mt-2 text-yellow-600">Admin data is incomplete</p>
                        <p class="mt-1 text-sm text-gray-500">Some admins could not be displayed</p>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error updating admin list:', error);
            listContainer.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-2 text-red-600">Failed to display admin list</p>
                    <p class="mt-1 text-sm text-gray-500">${error.message}</p>
                </div>
            `;
        }
    }

    /**
     * Focus on specific admin location
     * @param {number} lat - Latitude
     * @param {number} lng - Longitude
     */
    focusAdmin(lat, lng) {
        try {
            if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
                showNotification('Invalid location coordinates', 'error');
                return;
            }
            this.map.setView([lat, lng], 16);
        } catch (error) {
            console.error('Error focusing on admin location:', error);
            showNotification('Failed to focus on location', 'error');
        }
    }
}

// Initialize on DOM ready (only if on user track admin page)
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the user track admin page by looking for the map and admin-list elements
    const mapElement = document.getElementById('map');
    const adminListElement = document.getElementById('admin-list');
    const refreshBtn = document.getElementById('refresh-btn');
    
    // Only initialize if all three elements exist (user track admin page)
    if (mapElement && adminListElement && refreshBtn) {
        const apiKey = document.querySelector('[data-geoapify-key]')?.dataset.geoapifyKey;
        const userInfo = {
            name: document.querySelector('[data-user-name]')?.dataset.userName || '',
            address: document.querySelector('[data-user-address]')?.dataset.userAddress || '',
            adminLocationRoute: document.querySelector('[data-admin-location-route]')?.dataset.adminLocationRoute || ''
        };

        if (apiKey && userInfo.adminLocationRoute) {
            window.userTrackAdminMap = new UserTrackAdminMap(apiKey, userInfo);
            window.userTrackAdminMap.init();
        } else {
            console.error('Required data attributes not found for user track admin map');
        }
    }
});

// Expose refresh function globally for button onclick
window.refreshLocations = function() {
    if (window.userTrackAdminMap) {
        window.userTrackAdminMap.refreshLocations();
    }
};
