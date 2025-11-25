/**
 * User Forgot Password Form
 * Handles password reset with OTP verification using AJAX
 * Follows the 3-step pattern: Phone → OTP → Password
 */

import { MultiStepForm } from '../shared/multi-step-form.js';
import { PasswordValidator } from '../shared/password-validator.js';

class UserForgotPassword extends MultiStepForm {
    constructor() {
        super({
            totalSteps: 3,
            completedStepClass: 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold',
            activeStepClass: 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold',
            inactiveStepClass: 'w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold'
        });
        
        this.passwordValidator = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupPasswordValidation();
    }

    setupEventListeners() {
        // Check if we're on the forgot password page
        const sendOtpForm = document.getElementById('send-otp-form');
        const verifyOtpForm = document.getElementById('verify-otp-form');
        const resetPasswordForm = document.getElementById('reset-password-form');
        
        if (!sendOtpForm || !verifyOtpForm || !resetPasswordForm) {
            return; // Not on forgot password page
        }

        // Prevent default form submissions
        sendOtpForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSendOtp();
            return false;
        });

        verifyOtpForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleVerifyOtp();
            return false;
        });

        resetPasswordForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handlePasswordReset();
            return false;
        });

        // Expose methods globally for onclick handlers
        window.sendOTP = () => this.handleSendOtp();
        window.verifyOTP = () => this.handleVerifyOtp();
        window.resetPassword = () => this.handlePasswordReset();
        window.resendOTP = () => this.goToStep(1);
        window.goToStep = (step) => this.goToStep(step);
    }

    /**
     * Initialize password validation component for step 3
     */
    setupPasswordValidation() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        const matchText = document.getElementById('password-match-text');

        if (passwordInput && confirmPasswordInput && strengthBar && strengthText && matchText) {
            this.passwordValidator = new PasswordValidator({
                passwordInput,
                confirmPasswordInput,
                strengthBar,
                strengthText,
                matchText
            });
        }
    }

    /**
     * Populate hidden fields with verified data
     */
    populateHiddenFields() {
        const verifiedPhoneInput = document.getElementById('verified_phone');
        if (verifiedPhoneInput && this.verifiedData.phone) {
            verifiedPhoneInput.value = this.verifiedData.phone;
        }
    }

    /**
     * Step 1: Send OTP to phone number
     */
    async handleSendOtp() {
        const phoneInput = document.getElementById('phone');
        const button = document.getElementById('send-otp-btn');
        
        if (!phoneInput || !button) return;
        
        const phone = phoneInput.value.trim();

        // Validation
        if (!phone) {
            this.showError('phone-error', 'Please enter your phone number');
            return;
        }

        try {
            const result = await this._sendOTPRequest(
                document.querySelector('[data-send-otp-url]')?.dataset.sendOtpUrl || '/user/send-password-reset-otp',
                { phone },
                button,
                'phone-error'
            );

            if (result.success) {
                // Store verified phone
                this.verifiedData.phone = phone;
                
                // Move to OTP verification step
                this.goToStep(2);
            } else {
                this.showError('phone-error', result.data.message || 'Failed to send OTP. Please try again.');
            }
        } catch (error) {
            console.error('Error sending OTP:', error);
            this.showError('phone-error', 'Network error. Please check your connection and try again.');
        }
    }

    /**
     * Step 2: Verify OTP
     */
    async handleVerifyOtp() {
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
                document.querySelector('[data-verify-otp-url]')?.dataset.verifyOtpUrl || '/user/verify-password-reset-otp',
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
                
                // Populate hidden fields for step 3
                this.populateHiddenFields();
                
                // Move to password reset step
                this.goToStep(3);
            } else {
                this.showError('otp-error', result.data.message || 'Invalid OTP. Please try again.');
            }
        } catch (error) {
            console.error('Error verifying OTP:', error);
            this.showError('otp-error', 'Network error. Please check your connection and try again.');
        }
    }

    /**
     * Step 3: Reset password
     */
    async handlePasswordReset() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const button = document.getElementById('reset-password-btn');
        
        if (!passwordInput || !confirmPasswordInput || !button) return;
        
        const password = passwordInput.value;
        const passwordConfirmation = confirmPasswordInput.value;

        // Validation
        if (!password || password.length < 8) {
            this.showError('password-error', 'Password must be at least 8 characters long');
            return;
        }

        if (password !== passwordConfirmation) {
            this.showError('password-error', 'Passwords do not match');
            return;
        }

        // Disable button
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Resetting...';
        this.hideError('password-error');

        try {
            const response = await fetch(
                document.querySelector('[data-reset-password-url]')?.dataset.resetPasswordUrl || '/user/reset-password',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        phone: this.verifiedData.phone,
                        password: password,
                        password_confirmation: passwordConfirmation
                    })
                }
            );

            const result = await response.json();

            if (result.success === true || (response.status === 200 && result.success !== false)) {
                // Show success message
                this.hideError('password-error');
                const successDiv = document.createElement('div');
                successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
                successDiv.textContent = result.message || 'Password has been reset successfully. Redirecting to login...';
                document.getElementById('password-error')?.parentElement?.insertBefore(successDiv, document.getElementById('password-error'));
                
                // Redirect after a short delay to show the message
                setTimeout(() => {
                    window.location.href = result.redirect || '/user/login';
                }, 1500);
            } else {
                // Re-enable button
                button.disabled = false;
                button.textContent = originalText;
                
                this.showError('password-error', result.message || 'Failed to reset password. Please try again.');
            }
        } catch (error) {
            console.error('Error resetting password:', error);
            
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;
            
            this.showError('password-error', 'Network error. Please check your connection and try again.');
        }
    }
}

// Initialize on DOM ready (only if on forgot password page)
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the forgot password page
    if (document.getElementById('send-otp-form') && 
        document.getElementById('verify-otp-form') &&
        document.getElementById('reset-password-form')) {
        window.userForgotPassword = new UserForgotPassword();
    }
});
