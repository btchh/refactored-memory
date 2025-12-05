/**
 * Password Reset Wizard
 * Handles multi-step password reset with OTP verification
 */

export class PasswordResetWizard {
    constructor(options = {}) {
        this.currentStep = 1;
        this.verifiedPhone = '';
        this.verifiedOtp = '';
        this.routes = options.routes || {};
        
        this.init();
    }

    init() {
        // Prevent default form submissions
        const phoneForm = document.getElementById('phone-form');
        const otpForm = document.getElementById('otp-form');
        
        if (phoneForm) {
            phoneForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendOTP();
                return false;
            });
        }
        
        if (otpForm) {
            otpForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.verifyOTP();
                return false;
            });
        }

        // Initialize password validators
        this.initializePasswordValidators();
    }

    goToStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        
        // Show target step
        const targetStep = document.getElementById('step' + step);
        if (targetStep) {
            targetStep.classList.remove('hidden');
        }
        
        // Update progress indicators
        this.updateProgressIndicators(step);
        
        this.currentStep = step;
    }

    updateProgressIndicators(currentStep) {
        for (let i = 1; i <= 3; i++) {
            const indicator = document.getElementById('step' + i + '-indicator');
            const line = document.getElementById('progress-line-' + i);
            
            if (!indicator) continue;
            
            if (i < currentStep) {
                // Completed step
                indicator.className = 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold';
                if (line) line.className = 'flex-1 h-1 bg-green-600';
            } else if (i === currentStep) {
                // Current step
                indicator.className = 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold';
                if (line) line.className = 'flex-1 h-1 bg-gray-300';
            } else {
                // Future step
                indicator.className = 'w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold';
                if (line) line.className = 'flex-1 h-1 bg-gray-300';
            }
        }
    }

    showError(elementId, message) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative';
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
        // Also show in notification if on step 3
        if (this.currentStep === 3) {
            this.showNotification(message, 'error');
        }
    }

    hideError(elementId) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.classList.add('hidden');
        }
    }

    async sendOTP() {
        const phoneInput = document.getElementById('phone');
        const button = document.querySelector('#phone-form button[type="button"]');
        
        if (!phoneInput) return;
        
        const phone = phoneInput.value.trim();

        // Validation
        if (!phone) {
            this.showError('phone-error', 'Please enter your phone number');
            return;
        }

        // Disable button and show loading state
        if (button) {
            button.disabled = true;
            button.textContent = 'Sending...';
        }
        this.hideError('phone-error');

        try {
            const response = await fetch(this.routes.sendPasswordResetOtp, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone })
            });

            const data = await response.json();
            
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = 'Send OTP';
            }
            
            // Check if successful
            if (data.success === true) {
                // Store verified phone
                this.verifiedPhone = phone;
                
                // Move to step 2
                this.goToStep(2);
            } else {
                this.showError('phone-error', data.message || 'Failed to send OTP. Please try again.');
            }
        } catch (error) {
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = 'Send OTP';
            }
            this.showError('phone-error', 'Network error. Please check your connection and try again.');
        }
    }

    async verifyOTP() {
        const otpInput = document.getElementById('otp');
        const button = document.querySelector('#otp-form button[onclick*="verifyResetOTP"]');
        
        if (!otpInput) return;
        
        const otp = otpInput.value.trim();

        // Validation
        if (!otp || otp.length !== 6) {
            this.showError('otp-error', 'Please enter a valid 6-digit OTP code');
            return;
        }

        // Check if we have verified phone
        if (!this.verifiedPhone) {
            this.showError('otp-error', 'Session expired. Please start over.');
            setTimeout(() => this.goToStep(1), 2000);
            return;
        }

        // Disable button and show loading state
        if (button) {
            button.disabled = true;
            button.textContent = 'Verifying...';
        }
        this.hideError('otp-error');

        try {
            const response = await fetch(this.routes.verifyPasswordResetOtp, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    phone: this.verifiedPhone,
                    otp: otp 
                })
            });

            const data = await response.json();
            
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = 'Verify OTP';
            }
            
            // Check if verification successful
            if (data.success === true) {
                // Store verified OTP and populate hidden fields
                this.verifiedOtp = otp;
                
                const phoneField = document.getElementById('verified_phone');
                const otpField = document.getElementById('verified_otp');
                
                if (phoneField) phoneField.value = this.verifiedPhone;
                if (otpField) otpField.value = this.verifiedOtp;
                
                // Move to step 3
                this.goToStep(3);
            } else {
                this.showError('otp-error', data.message || 'Invalid OTP code. Please try again.');
            }
        } catch (error) {
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = 'Verify OTP';
            }
            this.showError('otp-error', 'Network error. Please check your connection and try again.');
        }
    }

    showNotification(message, type = 'error') {
        const notification = document.getElementById('reset-notification');
        if (!notification) return;
        
        const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        notification.className = `${bgColor} border px-4 py-3 rounded relative mb-4`;
        notification.textContent = message;
        notification.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            notification.classList.add('hidden');
        }, 5000);
    }

    checkPasswordStrength(password) {
        let strength = 0;
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        
        if (!strengthBar || !strengthText) return;
        
        if (password.length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.className = 'h-full transition-all duration-300';
            strengthText.textContent = '';
            return;
        }
        
        // Check password criteria
        if (password.length >= 8) strength += 25;
        if (password.match(/[a-z]/)) strength += 25;
        if (password.match(/[A-Z]/)) strength += 25;
        if (password.match(/[0-9]/)) strength += 15;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 10;
        
        strengthBar.style.width = strength + '%';
        
        if (strength < 40) {
            strengthBar.className = 'h-full transition-all duration-300 bg-red-500';
            strengthText.textContent = 'Weak password';
            strengthText.className = 'text-xs mt-1 text-red-500';
        } else if (strength < 70) {
            strengthBar.className = 'h-full transition-all duration-300 bg-yellow-500';
            strengthText.textContent = 'Medium password';
            strengthText.className = 'text-xs mt-1 text-yellow-600';
        } else {
            strengthBar.className = 'h-full transition-all duration-300 bg-green-500';
            strengthText.textContent = 'Strong password';
            strengthText.className = 'text-xs mt-1 text-green-600';
        }
    }

    checkPasswordMatch() {
        const password = document.getElementById('password');
        const confirmation = document.getElementById('password_confirmation');
        const matchText = document.getElementById('password-match-text');
        
        if (!password || !confirmation || !matchText) return;
        
        if (confirmation.value === '') {
            matchText.textContent = '';
            return;
        }
        
        if (password.value === confirmation.value) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'text-xs mt-2 text-green-600';
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'text-xs mt-2 text-red-600';
        }
    }

    initializePasswordValidators() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', () => {
                this.checkPasswordStrength(passwordInput.value);
                this.checkPasswordMatch();
            });
        }
        
        if (confirmInput) {
            confirmInput.addEventListener('input', () => this.checkPasswordMatch());
        }
    }

    reset() {
        this.currentStep = 1;
        this.verifiedPhone = '';
        this.verifiedOtp = '';
    }
}
