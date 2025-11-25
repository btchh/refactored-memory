/**
 * Multi-Step Form with OTP Verification
 * Shared utilities for multi-step registration/creation forms
 */

export class MultiStepForm {
    constructor(config) {
        this.currentStep = 1;
        this.totalSteps = config.totalSteps || 3;
        this.verifiedData = {};
        this.config = config;
    }

    /**
     * Navigate to a specific step
     * @param {number} step - Step number to navigate to
     */
    goToStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
        
        // Show target step
        const targetStep = document.getElementById(`step${step}`);
        if (targetStep) {
            targetStep.classList.remove('hidden');
        }
        
        // Update progress indicators
        this.updateProgressIndicators(step);
        
        this.currentStep = step;
    }

    /**
     * Update visual progress indicators
     * @param {number} currentStep - Current step number
     */
    updateProgressIndicators(currentStep) {
        for (let i = 1; i <= this.totalSteps; i++) {
            const indicator = document.getElementById(`step${i}-indicator`);
            const line = document.getElementById(`progress-line-${i}`);
            
            if (!indicator) continue;
            
            const completedClass = this.config.completedStepClass || 'w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold';
            const activeClass = this.config.activeStepClass || 'w-10 h-10 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center font-bold';
            const inactiveClass = this.config.inactiveStepClass || 'w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold';
            
            if (i < currentStep) {
                // Completed step
                indicator.className = completedClass;
                if (line) line.className = 'flex-1 h-1 bg-green-600';
            } else if (i === currentStep) {
                // Current step
                indicator.className = activeClass;
                if (line) line.className = 'flex-1 h-1 bg-gray-300';
            } else {
                // Future step
                indicator.className = inactiveClass;
                if (line) line.className = 'flex-1 h-1 bg-gray-300';
            }
        }
    }

    /**
     * Show error message
     * @param {string} elementId - ID of error element
     * @param {string} message - Error message
     */
    showError(elementId, message) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.remove('hidden');
        }
    }

    /**
     * Hide error message
     * @param {string} elementId - ID of error element
     */
    hideError(elementId) {
        const errorDiv = document.getElementById(elementId);
        if (errorDiv) {
            errorDiv.classList.add('hidden');
        }
    }

    /**
     * Internal method to send OTP request to API
     * This method is called by child classes to make OTP send requests.
     * Child classes should call this method using this._sendOTPRequest()
     * 
     * @param {string} url - API endpoint
     * @param {Object} data - Data to send
     * @param {HTMLElement} button - Button element
     * @param {string} errorElementId - Error element ID
     * @returns {Promise}
     * @private
     */
    async _sendOTPRequest(url, data, button, errorElementId) {
        // Disable button
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Sending...';
        this.hideError(errorElementId);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;

            // Handle Laravel validation errors (422 status)
            if (response.status === 422 && result.errors) {
                const firstError = Object.values(result.errors)[0];
                const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                return {
                    success: false,
                    data: { message: errorMessage },
                    status: response.status
                };
            }

            return {
                success: result.success === true || (response.status === 200 && result.success !== false),
                data: result,
                status: response.status
            };
        } catch (error) {
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;
            
            throw error;
        }
    }

    /**
     * Internal method to verify OTP with API
     * This method is called by child classes to make OTP verification requests.
     * Child classes should call this method using this._verifyOTPRequest()
     * 
     * @param {string} url - API endpoint
     * @param {Object} data - Data to send
     * @param {HTMLElement} button - Button element
     * @param {string} errorElementId - Error element ID
     * @returns {Promise}
     * @private
     */
    async _verifyOTPRequest(url, data, button, errorElementId) {
        // Disable button
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Verifying...';
        this.hideError(errorElementId);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;

            // Handle Laravel validation errors (422 status)
            if (response.status === 422 && result.errors) {
                const firstError = Object.values(result.errors)[0];
                const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                return {
                    success: false,
                    data: { message: errorMessage },
                    status: response.status
                };
            }

            return {
                success: result.success === true || (response.status === 200 && result.success !== false),
                data: result,
                status: response.status
            };
        } catch (error) {
            // Re-enable button
            button.disabled = false;
            button.textContent = originalText;
            
            throw error;
        }
    }

    /**
     * Reset form state
     */
    reset() {
        this.currentStep = 1;
        this.verifiedData = {};
        this.goToStep(1);
    }
}
