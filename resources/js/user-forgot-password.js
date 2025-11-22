/**
 * User Forgot Password Form
 * Handles password reset with OTP verification
 */

class UserForgotPassword {
    constructor() {
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupEventListeners());
        } else {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        // Check if we're on the forgot password page
        const sendOtpForm = document.getElementById('send-otp-form');
        const verifyOtpForm = document.getElementById('verify-otp-form');
        const phoneInput = document.getElementById('phone-input');
        const phoneHidden = document.getElementById('phone-hidden');
        const stepDescription = document.getElementById('step-description');
        const resendOtpBtn = document.getElementById('resend-otp');

        if (!sendOtpForm || !verifyOtpForm) {
            return; // Not on forgot password page
        }

        // Check if OTP was sent successfully (via data attribute)
        const otpSent = document.querySelector('[data-otp-sent]')?.dataset.otpSent === 'true';
        const savedPhone = document.querySelector('[data-saved-phone]')?.dataset.savedPhone;

        if (otpSent && savedPhone) {
            // Show OTP verification form
            sendOtpForm.style.display = 'none';
            verifyOtpForm.style.display = 'block';
            if (stepDescription) {
                stepDescription.textContent = 'Enter the OTP sent to your phone';
            }
            if (phoneHidden) {
                phoneHidden.value = savedPhone;
            }
        }

        // Resend OTP handler
        if (resendOtpBtn) {
            resendOtpBtn.addEventListener('click', () => {
                sendOtpForm.style.display = 'block';
                verifyOtpForm.style.display = 'none';
                if (stepDescription) {
                    stepDescription.textContent = 'Enter your phone number to receive an OTP';
                }
            });
        }
    }
}

// Initialize on DOM ready (only if on forgot password page)
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on the forgot password page
    if (document.getElementById('send-otp-form') && 
        document.getElementById('verify-otp-form')) {
        window.userForgotPassword = new UserForgotPassword();
    }
});
