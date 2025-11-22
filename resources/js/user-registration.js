/**
 * User Registration Form
 * Handles multi-step registration with OTP verification
 */

import { MultiStepForm } from './multi-step-form.js';
import { PasswordValidator } from './password-validator.js';

class UserRegistration extends MultiStepForm {
    constructor() {
        super({
            totalSteps: 3,
            completedStepClass: 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold',
            activeStepClass: 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold',
            inactiveStepClass: 'w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold'
        });
        
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Check if we're on the registration page
        const contactForm = document.getElementById('contact-form');
        const otpForm = document.getElementById('otp-form');
        const registrationForm = document.getElementById('registration-form');
        
        if (!contactForm || !otpForm || !registrationForm) {
            return; // Not on registration page
        }

        // Prevent default form submissions
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleContactSubmit();
            return false;
        });

        otpForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleOtpSubmit();
            return false;
        });

        // Setup password validation
        this.setupPasswordValidation();

        // Handle final form submission via AJAX
        registrationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleRegistrationSubmit();
        });

        // Expose methods globally for onclick handlers
        window.goToStep = (step) => this.goToStep(step);
        window.sendOTP = () => this.handleContactSubmit();
        window.verifyOTP = () => this.handleOtpSubmit();
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
        const verifiedOtpInput = document.getElementById('verified_otp');

        if (verifiedEmailInput) verifiedEmailInput.value = this.verifiedData.email || '';
        if (verifiedPhoneInput) verifiedPhoneInput.value = this.verifiedData.phone || '';
        if (verifiedOtpInput) verifiedOtpInput.value = this.verifiedData.otp || '';
    }

    async handleContactSubmit() {
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

        try {
            const result = await this._sendOTPRequest(
                document.querySelector('[data-send-otp-url]')?.dataset.sendOtpUrl || '/user/send-registration-otp',
                { email, phone },
                button,
                'contact-error'
            );

            if (result.success) {
                // Store verified contact info
                this.verifiedData.email = email;
                this.verifiedData.phone = phone;
                
                // Move to OTP step
                this.goToStep(2);
            } else {
                this.showError('contact-error', result.data.message || 'Failed to send OTP. Please try again.');
            }
        } catch (error) {
            console.error('Error sending OTP:', error);
            this.showError('contact-error', 'Network error. Please check your connection and try again.');
        }
    }

    async handleOtpSubmit() {
        const otpInput = document.getElementById('otp');
        const button = document.getElementById('verify-otp-btn');
        
        if (!otpInput || !button) return;
        
        const otp = otpInput.value.trim();

        // Validation
        if (!otp || otp.length !== 6) {
            this.showError('otp-error', 'Please enter a valid 6-digit OTP');
            return;
        }

        try {
            const result = await this._verifyOTPRequest(
                document.querySelector('[data-verify-otp-url]')?.dataset.verifyOtpUrl || '/user/verify-otp',
                { 
                    phone: this.verifiedData.phone,
                    otp: otp 
                },
                button,
                'otp-error'
            );

            if (result.success) {
                // Store verified OTP
                this.verifiedData.otp = otp;
                
                // Populate hidden fields now that we have all verified data
                this.populateHiddenFields();
                
                // Move to registration details step
                this.goToStep(3);
            } else {
                this.showError('otp-error', result.data.message || 'Invalid OTP. Please try again.');
            }
        } catch (error) {
            console.error('Error verifying OTP:', error);
            this.showError('otp-error', 'Network error. Please check your connection and try again.');
        }
    }

    async handleRegistrationSubmit() {
        const form = document.getElementById('registration-form');
        const submitButton = document.getElementById('register-submit-btn');
        const notificationDiv = document.getElementById('registration-notification');
        
        if (!form || !submitButton) return;

        // Disable submit button
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Creating Account...';

        // Hide any previous notifications
        if (notificationDiv) {
            notificationDiv.classList.add('hidden');
        }

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
                // Success - redirect to dashboard
                if (result.redirect) {
                    window.location.href = result.redirect;
                } else {
                    window.location.href = '/user/dashboard';
                }
            } else {
                // Show error message on step 3
                const errorMessage = result.message || 'Registration failed. Please check your information and try again.';
                
                if (notificationDiv) {
                    notificationDiv.textContent = errorMessage;
                    notificationDiv.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                    notificationDiv.classList.remove('hidden');
                }

                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        } catch (error) {
            console.error('Registration error:', error);
            
            if (notificationDiv) {
                notificationDiv.textContent = 'Network error. Please check your connection and try again.';
                notificationDiv.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                notificationDiv.classList.remove('hidden');
            }

            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    }
}

// Initialize on DOM ready (only if on registration page)
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the registration page
    if (document.getElementById('contact-form') && 
        document.getElementById('otp-form') && 
        document.getElementById('registration-form')) {
        window.userRegistration = new UserRegistration();
    }
});
