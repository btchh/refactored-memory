/**
 * Admin Creation Wizard
 * Handles multi-step admin creation with OTP verification
 */

export class AdminWizard {
    constructor(options = {}) {
        this.currentStep = 1;
        this.verifiedEmail = '';
        this.verifiedPhone = '';
        this.routes = options.routes || {};
        
        this.init();
    }

    init() {
        // Prevent default form submissions
        const contactForm = document.getElementById('contact-form');
        const otpForm = document.getElementById('otp-form');
        
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => {
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
            const label = indicator?.parentElement.querySelector('p');
            
            if (!indicator) continue;
            
            if (i < currentStep) {
                // Completed step
                indicator.className = 'w-12 h-12 mx-auto rounded-full bg-success text-white flex items-center justify-center font-bold text-lg';
                if (line) line.className = 'flex-1 h-2 bg-success rounded-full mx-2';
                if (label) label.className = 'text-sm mt-2 font-bold text-success';
            } else if (i === currentStep) {
                // Current step
                indicator.className = 'w-12 h-12 mx-auto rounded-full bg-wash text-white flex items-center justify-center font-bold text-lg';
                if (line) line.className = 'flex-1 h-2 bg-gray-300 rounded-full mx-2';
                if (label) label.className = 'text-sm mt-2 font-bold text-wash';
            } else {
                // Future step
                indicator.className = 'w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg';
                if (line) line.className = 'flex-1 h-2 bg-gray-300 rounded-full mx-2';
                if (label) label.className = 'text-sm mt-2 text-gray-500 font-bold';
            }
        }
    }

    showError(elementId, message) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
    }

    hideError(elementId) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.classList.add('hidden');
        }
    }

    async sendOTP() {
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const button = document.querySelector('#contact-form button[type="button"]');
        
        if (!emailInput || !phoneInput) return;
        
        const email = emailInput.value.trim();
        const phone = phoneInput.value.trim();

        // Validation
        if (!email || !phone) {
            this.showError('contact-error', 'Please fill in all fields');
            return;
        }

        // Disable button and show loading state
        if (button) {
            button.disabled = true;
            button.textContent = 'Sending...';
        }
        this.hideError('contact-error');

        try {
            const response = await fetch(this.routes.sendOtp, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, phone })
            });

            const data = await response.json();
            
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = '📤 Send OTP';
            }
            
            // Check if successful
            if (data.success === true || (response.status === 200 && data.success !== false)) {
                // Store verified contact info
                this.verifiedEmail = email;
                this.verifiedPhone = phone;
                
                // Move to step 2
                this.goToStep(2);
            } else {
                this.showError('contact-error', data.message || 'Failed to send OTP. Please try again.');
            }
        } catch (error) {
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = '📤 Send OTP';
            }
            this.showError('contact-error', 'Network error. Please check your connection and try again.');
        }
    }

    async verifyOTP() {
        const otpInput = document.getElementById('otp');
        const button = document.querySelector('#otp-form button[onclick*="verifyOTP"]');
        
        if (!otpInput) return;
        
        const otp = otpInput.value.trim();

        // Validation
        if (!otp || otp.length !== 6) {
            this.showError('otp-error', 'Please enter a valid 6-digit OTP code');
            return;
        }

        // Check if we have verified contact info
        if (!this.verifiedEmail || !this.verifiedPhone) {
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
            const response = await fetch(this.routes.verifyOtp, {
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
                button.textContent = '✅ Verify OTP';
            }
            
            // Check if verification successful
            if (data.success === true) {
                // Populate hidden fields
                const emailField = document.getElementById('verified_email');
                const phoneField = document.getElementById('verified_phone');
                
                if (emailField) emailField.value = this.verifiedEmail;
                if (phoneField) phoneField.value = this.verifiedPhone;
                
                // Move to step 3
                this.goToStep(3);
            } else {
                this.showError('otp-error', data.message || 'Invalid OTP code. Please try again.');
            }
        } catch (error) {
            // Re-enable button
            if (button) {
                button.disabled = false;
                button.textContent = '✅ Verify OTP';
            }
            this.showError('otp-error', 'Network error. Please check your connection and try again.');
        }
    }

    reset() {
        this.currentStep = 1;
        this.verifiedEmail = '';
        this.verifiedPhone = '';
    }
}
