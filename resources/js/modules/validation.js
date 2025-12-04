/**
 * Frontend Form Validation
 * Minimal validation - let backend handle most validation
 */

class FormValidator {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.errors = {};
        this.init();
    }

    init() {
        if (!this.form) return;
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    handleSubmit(e) {
        this.errors = {};
        this.clearErrors();

        if (!this.validate()) {
            e.preventDefault();
            this.displayErrors();
        }
    }

    validate() {
        const inputs = this.form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        inputs.forEach((input) => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        const name = field.name;
        const type = field.type;

        // Only check required fields
        if (!value) {
            this.errors[name] = `${this.getFieldLabel(name)} is required`;
            return false;
        }

        // Basic email validation
        if (type === 'email' && !this.isValidEmail(value)) {
            this.errors[name] = 'Please enter a valid email address';
            return false;
        }

        // Check password confirmation match
        if ((name === 'new_password_confirmation' || name === 'password_confirmation') && value) {
            const passwordField = this.form.querySelector('input[name="new_password"], input[name="password"]');
            if (passwordField && value !== passwordField.value) {
                this.errors[name] = 'Passwords do not match';
                return false;
            }
        }

        return true;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        const phoneRegex = /^[\d\s\-\+\(\)]+$/;
        return phoneRegex.test(phone) && phone.replace(/\D/g, '').length >= 10;
    }

    getFieldLabel(name) {
        const labels = {
            username: 'Username',
            branch_name: 'Branch Name',
            email: 'Email Address',
            password: 'Password',
            new_password: 'New Password',
            new_password_confirmation: 'Confirm Password',
            password_confirmation: 'Confirm Password',
            current_password: 'Current Password',
            fname: 'First Name',
            lname: 'Last Name',
            phone: 'Phone Number',
            address: 'Address',
        };
        return labels[name] || name.replace(/_/g, ' ').replace(/\b\w/g, (l) => l.toUpperCase());
    }

    displayErrors() {
        Object.keys(this.errors).forEach((fieldName) => {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('border-red-500', 'focus:ring-red-500');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
                errorDiv.textContent = this.errors[fieldName];
                field.parentNode.appendChild(errorDiv);
            }
        });
    }

    clearErrors() {
        const errorMessages = this.form.querySelectorAll('.error-message');
        errorMessages.forEach((msg) => msg.remove());

        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach((input) => {
            input.classList.remove('border-red-500', 'focus:ring-red-500');
        });
    }
}

// Real-time validation - minimal, only on blur
class RealtimeValidator {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.validator = new FormValidator(formSelector);
        this.init();
    }

    init() {
        if (!this.form) return;

        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach((input) => {
            input.addEventListener('blur', () => this.validateOnBlur(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    validateOnBlur(field) {
        const name = field.name;
        const value = field.value.trim();
        const type = field.type;
        const required = field.hasAttribute('required');

        this.clearFieldError(field);

        // Only validate if field is required and empty
        if (required && !value) {
            this.showFieldError(field, `${this.validator.getFieldLabel(name)} is required`);
            return;
        }

        // Only validate email format if it has a value
        if (type === 'email' && value && !this.validator.isValidEmail(value)) {
            this.showFieldError(field, 'Please enter a valid email address');
        }
    }

    showFieldError(field, message) {
        field.classList.add('border-red-500', 'focus:ring-red-500');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        const errorMsg = field.parentNode.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.remove();
            field.classList.remove('border-red-500', 'focus:ring-red-500');
        }
    }
}

// Initialize validators on page load
document.addEventListener('DOMContentLoaded', () => {
    // Login forms
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        new RealtimeValidator('form[action*="login"]');
    }

    // Profile forms
    const profileForm = document.querySelector('form[action*="profile"]');
    if (profileForm) {
        new RealtimeValidator('form[action*="profile"]');
    }

    // Password forms
    const passwordForm = document.querySelector('form[action*="password"]');
    if (passwordForm) {
        new RealtimeValidator('form[action*="password"]');
    }

    // Create admin/user forms
    const createForm = document.querySelector('form[action*="create"]');
    if (createForm) {
        new RealtimeValidator('form[action*="create"]');
    }

    // Register forms
    const registerForm = document.querySelector('form[action*="register"]');
    if (registerForm) {
        new RealtimeValidator('form[action*="register"]');
    }
});

// Export for use in other modules
export { FormValidator, RealtimeValidator };
