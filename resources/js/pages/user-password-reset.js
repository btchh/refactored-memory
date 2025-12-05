/**
 * User Password Reset Page
 * Initializes the password reset wizard
 */

import { PasswordResetWizard } from '../modules/password-reset-wizard.js';

// Initialize wizard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Get routes from data attributes or window object
    const routes = {
        sendPasswordResetOtp: window.routes?.sendPasswordResetOtp || '/user/send-password-reset-otp',
        verifyPasswordResetOtp: window.routes?.verifyPasswordResetOtp || '/user/verify-password-reset-otp'
    };

    // Create wizard instance
    const wizard = new PasswordResetWizard({ routes });

    // Expose functions globally for onclick handlers
    window.sendResetOTP = () => wizard.sendOTP();
    window.verifyResetOTP = () => wizard.verifyOTP();
    window.goToStep = (step) => wizard.goToStep(step);
});
