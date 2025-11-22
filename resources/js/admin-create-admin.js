/**
 * Admin Create Admin Form
 * Handles multi-step admin creation with OTP verification
 */

import { MultiStepForm } from './multi-step-form.js';
import { PasswordValidator } from './password-validator.js';

class AdminCreateAdmin extends MultiStepForm {
    constructor() {
        super({
            totalSteps: 3,
            completedStepClass: 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold',
            activeStepClass: 'w-10 h-10 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center font-bold',
            inactiveStepClass: 'w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold'
        });
        
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Check if we're on the create admin page
        const contactForm = document.getElementById('contact-form');
        const otpForm = document.getElementById('otp-form');
        const adminForm = document.querySelector('form[action*="create-admin"]');
        
        if (!contactForm || !otpForm || !adminForm) {
            return; // Not on create admin page
        }

        // Setup password validation
        this.setupPasswordValidation();

        // Handle final form submission via AJAX
        adminForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleAdminSubmit();
        });

        // Expose goToStep and sendOTP globally for onclick handlers
        window.goToStep = (step) => this.goToStep(step);
        window.sendOTP = () => this.handleSendOTP();
        window.verifyOTP = () => this.handleVerifyOTP();
    }

    setupPasswordValidation() {
        // Use shared PasswordValidator module
        new PasswordValidator({
            passwordInput: document.getElementById('password'),
            confirmPasswordInput: document.getElementById('password_confirmation'),
            strengthBar: document.getElementById('password-strength-bar'),
            strengthText: document.getElementById('password-strength-text'),
            matchText: document.getElementById('password-match-text')
        });
    }

    populateHiddenFields() {
        // Populate hidden fields with verified data
        const verifiedEmailInput = document.getElementById('verified_email');
        const verifiedPhoneInput = document.getElementById('verified_phone');

        if (verifiedEmailInput) verifiedEmailInput.value = this.verifiedData.email || '';
        if (verifiedPhoneInput) verifiedPhoneInput.value = this.verifiedData.phone || '';
    }

    async handleSendOTP() {
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const button = document.getElementById('send-otp-btn');
        
        if (!emailInput || !phoneInput || !button) return;
        
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();

        // Validation
        if (!email || !phone) {
            this.showError('contact-error', 'Please fill in all fields');
            return;
        }

        if (!button) {
            console.error('Button not found');
            return;
        }

        try {
            const result = await this._sendOTPRequest(
                document.querySelector('[data-send-otp-url]')?.dataset.sendOtpUrl || '/admin/send-admin-otp',
                { email, phone },
                button,
                'contact-error'
            );

            console.log('OTP send result:', result);

            if (result.success) {
                // Store verified contact info
                this.verifiedData.email = email;
                this.verifiedData.phone = phone;
                
                console.log('OTP sent successfully, moving to step 2');
                // Move to OTP step
                this.goToStep(2);
            } else {
                console.log('OTP send failed:', result.data.message);
                this.showError('contact-error', result.data.message || 'Failed to send OTP. Please try again.');
            }
        } catch (error) {
            console.error('Error sending OTP:', error);
            this.showError('contact-error', 'Network error. Please check your connection and try again.');
        }
    }

    async handleVerifyOTP() {
        const otpInput = document.getElementById('otp');
        const button = document.getElementById('verify-otp-btn');
        
        if (!otpInput || !button) return;
        
        const otp = otpInput.value.trim();

        // Validation
        if (!otp || otp.length !== 6) {
            this.showError('otp-error', 'Please enter a valid 6-digit OTP');
            return;
        }

        if (!button) {
            console.error('Button not found');
            return;
        }

        try {
            const result = await this._verifyOTPRequest(
                document.querySelector('[data-verify-otp-url]')?.dataset.verifyOtpUrl || '/admin/verify-admin-otp',
                { 
                    phone: this.verifiedData.phone,
                    otp: otp 
                },
                button,
                'otp-error'
            );

            console.log('OTP verify result:', result);

            if (result.success) {
                // Store verified OTP
                this.verifiedData.otp = otp;
                
                // Populate hidden fields now that we have all verified data
                this.populateHiddenFields();
                
                console.log('OTP verified successfully, moving to step 3');
                // Move to admin details step
                this.goToStep(3);
            } else {
                console.log('OTP verification failed:', result.data.message);
                this.showError('otp-error', result.data.message || 'Invalid OTP. Please try again.');
            }
        } catch (error) {
            console.error('Error verifying OTP:', error);
            this.showError('otp-error', 'Network error. Please check your connection and try again.');
        }
    }

    async handleAdminSubmit() {
        const form = document.querySelector('form[action*="create-admin"]');
        const submitButton = form?.querySelector('button[type="submit"]');
        
        if (!form || !submitButton) return;

        // Disable submit button
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Creating Admin...';

        try {
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success !== false) {
                // Success - show message and redirect or reset form
                alert(result.message || 'Admin created successfully!');
                
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    // Reset form and go back to step 1
                    form.reset();
                    this.reset();
                }
            } else {
                // Show error message on step 3
                const errorMessage = result.message || 'Failed to create admin. Please check your information and try again.';
                alert(errorMessage);

                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        } catch (error) {
            console.error('Admin creation error:', error);
            alert('Network error. Please check your connection and try again.');

            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    }
}

// Initialize on DOM ready (only if on create admin page)
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the create admin page
    if (document.getElementById('contact-form') && 
        document.getElementById('otp-form') && 
        document.getElementById('admin-form')) {
        window.adminCreateAdmin = new AdminCreateAdmin();
    }
});
