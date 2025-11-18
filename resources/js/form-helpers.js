/**
 * Form Helper Utilities
 */

/**
 * Show a toast notification
 */
export function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    const bgColor = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500',
    }[type] || 'bg-blue-500';

    toast.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('animate-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/**
 * Disable form submission button during processing
 */
export function disableFormButton(formSelector, buttonText = 'Processing...') {
    const form = document.querySelector(formSelector);
    if (!form) return;

    const button = form.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = true;
        button.dataset.originalText = button.textContent;
        button.textContent = buttonText;
        button.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

/**
 * Enable form submission button
 */
export function enableFormButton(formSelector) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    const button = form.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = false;
        button.textContent = button.dataset.originalText || 'Submit';
        button.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

/**
 * Clear form fields
 */
export function clearForm(formSelector) {
    const form = document.querySelector(formSelector);
    if (form) {
        form.reset();
    }
}

/**
 * Populate form with data
 */
export function populateForm(formSelector, data) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    Object.keys(data).forEach((key) => {
        const field = form.querySelector(`[name="${key}"]`);
        if (field) {
            if (field.type === 'checkbox') {
                field.checked = data[key];
            } else if (field.type === 'radio') {
                const radio = form.querySelector(`[name="${key}"][value="${data[key]}"]`);
                if (radio) radio.checked = true;
            } else {
                field.value = data[key];
            }
        }
    });
}

/**
 * Get form data as object
 */
export function getFormData(formSelector) {
    const form = document.querySelector(formSelector);
    if (!form) return {};

    const formData = new FormData(form);
    const data = {};

    formData.forEach((value, key) => {
        if (data[key]) {
            if (Array.isArray(data[key])) {
                data[key].push(value);
            } else {
                data[key] = [data[key], value];
            }
        } else {
            data[key] = value;
        }
    });

    return data;
}

/**
 * Add loading state to element
 */
export function setLoading(selector, isLoading = true) {
    const element = document.querySelector(selector);
    if (!element) return;

    if (isLoading) {
        element.classList.add('opacity-50', 'pointer-events-none');
        element.dataset.loading = 'true';
    } else {
        element.classList.remove('opacity-50', 'pointer-events-none');
        element.dataset.loading = 'false';
    }
}

/**
 * Validate email format
 */
export function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone format
 */
export function isValidPhone(phone) {
    const phoneRegex = /^[\d\s\-\+\(\)]+$/;
    return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

/**
 * Validate password strength
 */
export function validatePasswordStrength(password) {
    const strength = {
        score: 0,
        feedback: [],
    };

    if (password.length >= 8) strength.score++;
    else strength.feedback.push('At least 8 characters');

    if (password.length >= 12) strength.score++;
    else strength.feedback.push('At least 12 characters for better security');

    if (/[a-z]/.test(password)) strength.score++;
    else strength.feedback.push('Add lowercase letters');

    if (/[A-Z]/.test(password)) strength.score++;
    else strength.feedback.push('Add uppercase letters');

    if (/\d/.test(password)) strength.score++;
    else strength.feedback.push('Add numbers');

    if (/[!@#$%^&*]/.test(password)) strength.score++;
    else strength.feedback.push('Add special characters (!@#$%^&*)');

    return {
        score: strength.score,
        level: strength.score <= 2 ? 'weak' : strength.score <= 4 ? 'medium' : 'strong',
        feedback: strength.feedback,
    };
}

/**
 * Show password strength indicator
 */
export function showPasswordStrength(inputSelector, indicatorSelector) {
    const input = document.querySelector(inputSelector);
    const indicator = document.querySelector(indicatorSelector);

    if (!input || !indicator) return;

    input.addEventListener('input', () => {
        const strength = validatePasswordStrength(input.value);
        const colors = {
            weak: 'bg-red-500',
            medium: 'bg-yellow-500',
            strong: 'bg-green-500',
        };

        indicator.className = `h-2 rounded-full transition-all ${colors[strength.level]}`;
        indicator.style.width = `${(strength.score / 6) * 100}%`;
    });
}

/**
 * Format phone number
 */
export function formatPhoneNumber(phone) {
    const cleaned = phone.replace(/\D/g, '');
    if (cleaned.length === 10) {
        return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6)}`;
    }
    return phone;
}

/**
 * Debounce function for input events
 */
export function debounce(func, wait) {
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
 * Throttle function for scroll/resize events
 */
export function throttle(func, limit) {
    let inThrottle;
    return function (...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}
