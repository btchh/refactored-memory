/**
 * Centralized Token Management Utility
 * Handles CSRF tokens and other authentication tokens consistently across the application
 */

class TokenManager {
    constructor() {
        this.csrfToken = null;
        this.sessionToken = null;
        this.init();
    }

    /**
     * Initialize token manager
     */
    init() {
        this.loadCsrfToken();
        this.loadSessionToken();
        this.setupTokenRefresh();
    }

    /**
     * Load CSRF token from meta tag
     */
    loadCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            this.csrfToken = metaTag.getAttribute('content');
            console.log('[TokenManager] CSRF token loaded');
        } else {
            console.warn('[TokenManager] CSRF token meta tag not found. Make sure it\'s included in your layout.');
        }
    }

    /**
     * Load session token from session storage
     */
    loadSessionToken() {
        try {
            this.sessionToken = sessionStorage.getItem('auth_session_token');
            if (this.sessionToken) {
                console.log('[TokenManager] Session token loaded');
            }
        } catch (e) {
            console.warn('[TokenManager] Failed to load session token:', e);
        }
    }

    /**
     * Setup automatic token refresh
     */
    setupTokenRefresh() {
        // Refresh tokens every 10 minutes
        setInterval(() => {
            this.refreshCsrfToken();
            this.loadSessionToken();
        }, 600000);

        // Refresh on page visibility change
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshCsrfToken();
                this.loadSessionToken();
            }
        });
    }

    /**
     * Get CSRF token
     * @returns {string|null} CSRF token
     */
    getCsrfToken() {
        if (!this.csrfToken) {
            this.loadCsrfToken();
        }
        return this.csrfToken;
    }

    /**
     * Get session token
     * @returns {string|null} Session token
     */
    getSessionToken() {
        if (!this.sessionToken) {
            this.loadSessionToken();
        }
        return this.sessionToken;
    }

    /**
     * Get default headers for API requests
     * @returns {Object} Headers object with CSRF token
     */
    getDefaultHeaders() {
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        const csrfToken = this.getCsrfToken();
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const sessionToken = this.getSessionToken();
        if (sessionToken) {
            headers['X-Session-Token'] = sessionToken;
        }

        return headers;
    }

    /**
     * Make authenticated fetch request
     * @param {string} url - Request URL
     * @param {Object} options - Fetch options
     * @returns {Promise<Response>} Fetch response
     */
    async fetch(url, options = {}) {
        const defaultOptions = {
            credentials: 'include',
            headers: this.getDefaultHeaders(),
        };

        // Merge headers
        if (options.headers) {
            defaultOptions.headers = { ...defaultOptions.headers, ...options.headers };
        }

        // Merge all options
        const finalOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, finalOptions);

            // Handle 401 Unauthorized
            if (response.status === 401) {
                console.warn('[TokenManager] Unauthorized - session may have expired');
                this.handleUnauthorized();
            }

            return response;
        } catch (error) {
            console.error('[TokenManager] Fetch error:', error);
            throw error;
        }
    }

    /**
     * Make authenticated POST request
     * @param {string} url - Request URL
     * @param {Object} data - Request data
     * @param {Object} options - Additional options
     * @returns {Promise<Response>} Fetch response
     */
    async post(url, data = {}, options = {}) {
        return this.fetch(url, {
            method: 'POST',
            body: JSON.stringify(data),
            ...options,
        });
    }

    /**
     * Make authenticated PUT request
     * @param {string} url - Request URL
     * @param {Object} data - Request data
     * @param {Object} options - Additional options
     * @returns {Promise<Response>} Fetch response
     */
    async put(url, data = {}, options = {}) {
        return this.fetch(url, {
            method: 'PUT',
            body: JSON.stringify(data),
            ...options,
        });
    }

    /**
     * Make authenticated GET request
     * @param {string} url - Request URL
     * @param {Object} options - Additional options
     * @returns {Promise<Response>} Fetch response
     */
    async get(url, options = {}) {
        return this.fetch(url, {
            method: 'GET',
            ...options,
        });
    }

    /**
     * Make authenticated DELETE request
     * @param {string} url - Request URL
     * @param {Object} options - Additional options
     * @returns {Promise<Response>} Fetch response
     */
    async delete(url, options = {}) {
        return this.fetch(url, {
            method: 'DELETE',
            ...options,
        });
    }

    /**
     * Refresh CSRF token (useful after token rotation)
     */
    refreshCsrfToken() {
        this.csrfToken = null;
        this.loadCsrfToken();
    }

    /**
     * Handle unauthorized access
     */
    handleUnauthorized() {
        // Clear tokens
        this.csrfToken = null;
        this.sessionToken = null;

        try {
            sessionStorage.removeItem('auth_session_token');
            localStorage.removeItem('app_session_sync');
        } catch (e) {
            console.warn('[TokenManager] Failed to clear tokens:', e);
        }

        // Redirect to appropriate login page
        const currentPath = window.location.pathname;
        if (currentPath.includes('/admin/')) {
            window.location.href = '/admin/login';
        } else if (currentPath.includes('/user/')) {
            window.location.href = '/user/login';
        } else {
            window.location.href = '/';
        }
    }

    /**
     * Set session token (called after login)
     * @param {string} token - Session token
     */
    setSessionToken(token) {
        this.sessionToken = token;
        try {
            sessionStorage.setItem('auth_session_token', token);
            console.log('[TokenManager] Session token set');
        } catch (e) {
            console.warn('[TokenManager] Failed to set session token:', e);
        }
    }

    /**
     * Clear all tokens (called on logout)
     */
    clearTokens() {
        this.csrfToken = null;
        this.sessionToken = null;

        try {
            sessionStorage.removeItem('auth_session_token');
            localStorage.removeItem('app_session_sync');
        } catch (e) {
            console.warn('[TokenManager] Failed to clear tokens:', e);
        }

        console.log('[TokenManager] All tokens cleared');
    }

    /**
     * Get token status
     */
    getStatus() {
        return {
            csrfToken: !!this.csrfToken,
            sessionToken: !!this.sessionToken,
            timestamp: new Date().toISOString(),
        };
    }
}

// Create global instance
window.TokenManager = new TokenManager();

// Export for module usage
export default TokenManager;
