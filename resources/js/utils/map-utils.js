/**
 * Shared Map Utilities
 * Common functions for map initialization and error handling
 */

/**
 * Show notification message
 * @param {string} message - The message to display
 * @param {string} type - Type of notification: 'info', 'success', 'error', 'warning'
 * @param {number} duration - Duration in milliseconds (0 for persistent)
 * @param {HTMLElement|null} container - Container element (null for fixed position)
 */
export function showNotification(message, type = 'info', duration = 5000, container = null) {
    const colorClasses = {
        error: 'bg-red-50 text-red-700 border-red-200',
        success: 'bg-green-50 text-green-700 border-green-200',
        warning: 'bg-yellow-50 text-yellow-700 border-yellow-200',
        info: 'bg-blue-50 text-blue-700 border-blue-200'
    };

    const icons = {
        error: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
        success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
        warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
        info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
    };

    const notification = document.createElement('div');
    const colorClass = colorClasses[type] || colorClasses.info;
    const icon = icons[type] || icons.info;

    if (container) {
        // Inline notification
        notification.className = `p-4 rounded-lg border ${colorClass} mb-3`;
        notification.innerHTML = `
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    ${icon}
                </svg>
                <span>${message}</span>
            </div>
        `;
        container.insertBefore(notification, container.firstChild);
    } else {
        // Fixed position notification
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg border ${colorClass} shadow-lg max-w-md`;
        notification.innerHTML = `
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    ${icon}
                </svg>
                <div class="flex-1">
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
    }

    // Auto-remove
    if (duration > 0) {
        setTimeout(() => notification.remove(), duration);
    }
}

/**
 * Initialize Leaflet map with error handling
 * @param {string} elementId - ID of the map container element
 * @param {Array} center - [latitude, longitude] for map center
 * @param {number} zoom - Initial zoom level
 * @param {string} apiKey - Geoapify API key
 * @returns {Object|null} - Leaflet map instance or null on error
 */
export function initializeMap(elementId, center, zoom, apiKey) {
    try {
        const map = L.map(elementId).setView(center, zoom);

        L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${apiKey}`, {
            attribution: '© <a href="https://www.geoapify.com/">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 20
        }).addTo(map).on('tileerror', function(error) {
            console.error('Map tile loading error:', error);
            showNotification('Map tiles failed to load. Please check your internet connection.', 'error');
        });

        return map;
    } catch (error) {
        console.error('Map initialization error:', error);
        showNotification('Failed to initialize map. Please refresh the page.', 'error');
        return null;
    }
}

/**
 * Fetch data with retry mechanism
 * @param {string} url - URL to fetch
 * @param {Object} options - Fetch options
 * @param {number} maxRetries - Maximum number of retries
 * @param {Function} onRetry - Callback for retry attempts
 * @returns {Promise} - Promise resolving to response data
 */
export async function fetchWithRetry(url, options = {}, maxRetries = 2, onRetry = null) {
    let retryCount = 0;

    const defaultOptions = {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin',
        ...options
    };

    const attemptFetch = async () => {
        try {
            const response = await fetch(url, defaultOptions);
            console.log('Response status:', response.status);

            if (!response.ok) {
                // Try to get error message from response
                try {
                    const data = await response.json();
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                } catch (err) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            }

            return await response.json();
        } catch (error) {
            if (retryCount < maxRetries) {
                retryCount++;
                console.log(`Retrying... Attempt ${retryCount} of ${maxRetries}`);
                
                if (onRetry) {
                    onRetry(retryCount, maxRetries);
                }

                // Exponential backoff
                await new Promise(resolve => setTimeout(resolve, 2000 * retryCount));
                return attemptFetch();
            }
            throw error;
        }
    };

    return attemptFetch();
}

/**
 * Create a loading spinner HTML
 * @param {string} message - Loading message
 * @returns {string} - HTML string for loading spinner
 */
export function createLoadingSpinner(message = 'Loading...') {
    return `
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-3 text-gray-600">${message}</span>
        </div>
    `;
}

/**
 * Create an error display HTML
 * @param {string} title - Error title
 * @param {string} message - Error message
 * @param {Function|null} onRetry - Retry callback function
 * @returns {string} - HTML string for error display
 */
export function createErrorDisplay(title, message, onRetry = null) {
    return `
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="mt-2 text-red-600 font-semibold">${title}</p>
            <p class="mt-1 text-sm text-gray-600">${message}</p>
            ${onRetry ? `
                <button onclick="${onRetry}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Try Again
                </button>
            ` : ''}
        </div>
    `;
}

/**
 * Create custom marker icon
 * @param {string} color - Marker color (hex)
 * @returns {Object} - Leaflet divIcon
 */
export function createMarkerIcon(color) {
    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
        iconSize: [30, 30]
    });
}

/**
 * Clear map layers
 * @param {Object} map - Leaflet map instance
 * @param {Array} markers - Array of marker objects
 * @param {Array} layers - Array of layer objects
 */
export function clearMapLayers(map, markers, layers) {
    markers.forEach(marker => map.removeLayer(marker));
    markers.length = 0;
    
    layers.forEach(layer => map.removeLayer(layer));
    layers.length = 0;
}
