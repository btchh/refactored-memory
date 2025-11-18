/**
 * Utility Functions for Admin Panel
 */

/**
 * Make an AJAX request
 */
async function apiCall(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    };

    try {
        const response = await fetch(url, { ...defaultOptions, ...options });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Request failed');
        }

        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

/**
 * Validate email
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validate phone number
 */
function isValidPhone(phone) {
    const regex = /^[\d\s\-\+\(\)]+$/;
    return regex.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle function
 */
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Get URL parameters
 */
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (let [key, value] of params) {
        result[key] = value;
    }
    return result;
}

/**
 * Copy to clipboard
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        window.AdminUI?.showToast('Copied to clipboard!', 'success');
        return true;
    } catch (err) {
        console.error('Failed to copy:', err);
        window.AdminUI?.showToast('Failed to copy', 'error');
        return false;
    }
}

/**
 * Generate random ID
 */
function generateId() {
    return Math.random().toString(36).substr(2, 9);
}

/**
 * Check if element is in viewport
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Smooth scroll to element
 */
function smoothScrollTo(element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Get element by data attribute
 */
function getByData(key, value) {
    return document.querySelector(`[data-${key}="${value}"]`);
}

/**
 * Get all elements by data attribute
 */
function getAllByData(key, value) {
    return document.querySelectorAll(`[data-${key}="${value}"]`);
}

/**
 * Add event listener to multiple elements
 */
function addEventListenerToAll(selector, event, callback) {
    document.querySelectorAll(selector).forEach(element => {
        element.addEventListener(event, callback);
    });
}

/**
 * Toggle class on element
 */
function toggleClass(element, className) {
    element.classList.toggle(className);
}

/**
 * Add class to element
 */
function addClass(element, className) {
    element.classList.add(className);
}

/**
 * Remove class from element
 */
function removeClass(element, className) {
    element.classList.remove(className);
}

/**
 * Check if element has class
 */
function hasClass(element, className) {
    return element.classList.contains(className);
}

/**
 * Get element's computed style
 */
function getStyle(element, property) {
    return window.getComputedStyle(element).getPropertyValue(property);
}

/**
 * Set multiple styles on element
 */
function setStyles(element, styles) {
    Object.assign(element.style, styles);
}

/**
 * Create element with attributes
 */
function createElement(tag, attributes = {}, content = '') {
    const element = document.createElement(tag);
    Object.entries(attributes).forEach(([key, value]) => {
        if (key === 'class') {
            element.className = value;
        } else if (key === 'style') {
            Object.assign(element.style, value);
        } else {
            element.setAttribute(key, value);
        }
    });
    if (content) element.innerHTML = content;
    return element;
}

/**
 * Parse JSON safely
 */
function parseJSON(str, fallback = null) {
    try {
        return JSON.parse(str);
    } catch (e) {
        console.error('JSON Parse Error:', e);
        return fallback;
    }
}

/**
 * Deep clone object
 */
function deepClone(obj) {
    return JSON.parse(JSON.stringify(obj));
}

/**
 * Merge objects
 */
function mergeObjects(target, source) {
    return { ...target, ...source };
}

/**
 * Check if object is empty
 */
function isEmpty(obj) {
    return Object.keys(obj).length === 0;
}

/**
 * Get object keys
 */
function getKeys(obj) {
    return Object.keys(obj);
}

/**
 * Get object values
 */
function getValues(obj) {
    return Object.values(obj);
}

/**
 * Filter object by keys
 */
function filterObject(obj, keys) {
    return keys.reduce((result, key) => {
        if (key in obj) result[key] = obj[key];
        return result;
    }, {});
}

/**
 * Capitalize string
 */
function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Truncate string
 */
function truncate(str, length = 50) {
    return str.length > length ? str.substring(0, length) + '...' : str;
}

/**
 * Slugify string
 */
function slugify(str) {
    return str
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * Wait for milliseconds
 */
function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Retry function
 */
async function retry(fn, retries = 3, delay = 1000) {
    for (let i = 0; i < retries; i++) {
        try {
            return await fn();
        } catch (error) {
            if (i === retries - 1) throw error;
            await wait(delay);
        }
    }
}
