/**
 * User Registration Page
 * Initializes the registration wizard
 */

import { RegistrationWizard } from '../modules/registration-wizard.js';

// Initialize wizard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Get routes from data attributes or window object
    const routes = {
        sendOtp: window.routes?.sendRegistrationOtp || '/user/send-registration-otp',
        verifyOtp: window.routes?.verifyOtp || '/user/verify-otp'
    };

    // Create wizard instance
    const wizard = new RegistrationWizard({ routes });

    // Expose functions globally for onclick handlers
    window.sendOTP = () => wizard.sendOTP();
    window.verifyOTP = () => wizard.verifyOTP();
    window.goToStep = (step) => wizard.goToStep(step);
});
