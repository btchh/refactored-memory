/**
 * Admin Creation Page
 * Initializes the admin creation wizard
 */

import { AdminWizard } from '../modules/admin-wizard.js';

// Initialize wizard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Get routes from data attributes or window object
    const routes = {
        sendOtp: window.routes?.sendAdminOtp || '/admin/send-admin-otp',
        verifyOtp: window.routes?.verifyAdminOtp || '/admin/verify-admin-otp'
    };

    // Create wizard instance
    const wizard = new AdminWizard({ routes });

    // Expose functions globally for onclick handlers
    window.sendOTP = () => wizard.sendOTP();
    window.verifyOTP = () => wizard.verifyOTP();
    window.goToStep = (step) => wizard.goToStep(step);
});
