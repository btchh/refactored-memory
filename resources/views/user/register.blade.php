<x-guest-layout>
    <x-slot:title>User Registration</x-slot:title>
    <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <x-modules.card class="shadow-xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
                    <p class="text-gray-600">Join us today</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-10 h-10 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center font-bold">1</div>
                            <p class="text-sm mt-2 font-semibold text-green-600">Contact Info</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                            <p class="text-sm mt-2 text-gray-500">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                            <p class="text-sm mt-2 text-gray-500">User Details</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Contact Information -->
                <div id="step1" class="step-content">
                    <h2 class="text-xl font-bold mb-4">Step 1: Contact Information</h2>
                    <form id="contact-form" class="space-y-4">
                        <x-modules.input 
                            type="email" 
                            id="email" 
                            name="email" 
                            label="Email Address" 
                            placeholder="your@email.com"
                            required 
                        />

                        <x-modules.input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            label="Phone Number" 
                            placeholder="09123456789"
                            required 
                        />

                        <div id="contact-error" class="hidden text-red-600 text-sm"></div>

                        <x-modules.button type="button" onclick="sendOTP()" variant="primary" fullWidth>
                            Send OTP
                        </x-modules.button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="step-content hidden">
                    <h2 class="text-xl font-bold mb-4">Step 2: Verify OTP</h2>
                    <p class="text-gray-600 mb-4">Enter the OTP sent to your phone</p>
                    <form id="otp-form" class="space-y-4">
                        <x-modules.input 
                            type="text" 
                            id="otp" 
                            name="otp" 
                            label="OTP Code" 
                            placeholder="Enter 6-digit code"
                            maxlength="6"
                            required 
                        />

                        <div id="otp-error" class="hidden text-red-600 text-sm"></div>

                        <div class="flex gap-3">
                            <x-modules.button type="button" onclick="goToStep(1)" variant="secondary" class="flex-1">
                                Back
                            </x-modules.button>
                            <x-modules.button type="button" onclick="verifyOTP()" variant="primary" class="flex-1">
                                Verify OTP
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: User Details -->
                <div id="step3" class="step-content hidden">
                    <h2 class="text-xl font-bold mb-4">Step 3: Complete Your Profile</h2>
                    
                    <!-- Notification Area -->
                    <div id="registration-notification" class="hidden mb-4"></div>
                    
                    <form action="{{ route('user.register') }}" method="POST" class="space-y-4" id="registration-form">
                        @csrf
                        <input type="hidden" id="verified_email" name="email">
                        <input type="hidden" id="verified_phone" name="phone">
                        <input type="hidden" id="verified_otp" name="otp">

                        <x-modules.input 
                            type="text" 
                            id="username"
                            name="username" 
                            label="Username" 
                            placeholder="Choose a username"
                            required 
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-modules.input 
                                type="text" 
                                id="fname"
                                name="fname" 
                                label="First Name" 
                                placeholder="First name"
                                required 
                            />

                            <x-modules.input 
                                type="text" 
                                id="lname"
                                name="lname" 
                                label="Last Name" 
                                placeholder="Last name"
                                required 
                            />
                        </div>

                        <x-modules.input 
                            type="text" 
                            id="address"
                            name="address" 
                            label="Address" 
                            placeholder="Your full address"
                            required 
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-modules.input 
                                    type="password" 
                                    id="password"
                                    name="password" 
                                    label="Password" 
                                    placeholder="Min 8 characters"
                                    required 
                                />
                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="password-strength-bar" class="h-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="password-strength-text" class="text-xs mt-1 text-gray-500"></p>
                                </div>
                            </div>

                            <div>
                                <x-modules.input 
                                    type="password" 
                                    id="password_confirmation"
                                    name="password_confirmation" 
                                    label="Confirm Password" 
                                    placeholder="Confirm password"
                                    required 
                                />
                                <!-- Password Match Indicator -->
                                <p id="password-match-text" class="text-xs mt-2"></p>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <x-modules.button type="button" onclick="goToStep(2)" variant="secondary" class="flex-1">
                                Back
                            </x-modules.button>
                            <x-modules.button type="submit" variant="success" class="flex-1" id="register-submit-btn">
                                Create Account
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Already have an account?
                        <a href="{{ route('user.login') }}" class="text-green-600 hover:text-green-700 font-medium">
                            Sign in here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>

    <script>
        // Registration state management
        const RegistrationState = {
            currentStep: 1,
            verifiedEmail: '',
            verifiedPhone: '',
            verifiedOtp: '',
            
            reset() {
                this.currentStep = 1;
                this.verifiedEmail = '';
                this.verifiedPhone = '';
                this.verifiedOtp = '';
            }
        };

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent default form submissions
            const contactForm = document.getElementById('contact-form');
            const otpForm = document.getElementById('otp-form');
            
            if (contactForm) {
                contactForm.addEventListener('submit', handleContactSubmit);
            }
            
            if (otpForm) {
                otpForm.addEventListener('submit', handleOtpSubmit);
            }
        });

        // Handle contact form submission
        function handleContactSubmit(e) {
            e.preventDefault();
            sendOTP();
            return false;
        }

        // Handle OTP form submission
        function handleOtpSubmit(e) {
            e.preventDefault();
            verifyOTP();
            return false;
        }

        // Navigate to specific step
        function goToStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
            
            // Show target step
            const targetStep = document.getElementById('step' + step);
            if (targetStep) {
                targetStep.classList.remove('hidden');
            }
            
            // Update progress indicators
            updateProgressIndicators(step);
            
            RegistrationState.currentStep = step;
        }

        // Update visual progress indicators
        function updateProgressIndicators(currentStep) {
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

        // Show error message
        function showError(elementId, message) {
            const errorDiv = document.getElementById(elementId);
            if (errorDiv) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }
            // Also show in notification if on step 3
            if (RegistrationState.currentStep === 3) {
                showNotification(message, 'error');
            }
        }

        // Hide error message
        function hideError(elementId) {
            const errorDiv = document.getElementById(elementId);
            if (errorDiv) {
                errorDiv.classList.add('hidden');
            }
        }

        // Send OTP to phone
        function sendOTP() {
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const button = document.querySelector('#contact-form button[type="button"]');
            
            if (!emailInput || !phoneInput) return;
            
            const email = emailInput.value.trim();
            const phone = phoneInput.value.trim();

            // Validation
            if (!email || !phone) {
                showError('contact-error', 'Please fill in all fields');
                return;
            }

            // Disable button and show loading state
            if (button) {
                button.disabled = true;
                button.textContent = 'Sending...';
            }
            hideError('contact-error');

            // Make API request
            fetch('{{ route('user.send-registration-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, phone })
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                // Re-enable button
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Send OTP';
                }
                
                // Check if successful
                if (data.success === true) {
                    // Store verified contact info
                    RegistrationState.verifiedEmail = email;
                    RegistrationState.verifiedPhone = phone;
                    
                    // Move to step 2
                    goToStep(2);
                } else {
                    showError('contact-error', data.message || 'Failed to send OTP. Please try again.');
                }
            })
            .catch(error => {
                // Re-enable button
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Send OTP';
                }
                showError('contact-error', 'Network error. Please check your connection and try again.');
            });
        }

        // Show notification
        function showNotification(message, type = 'error') {
            const notification = document.getElementById('registration-notification');
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

        // Password strength checker
        function checkPasswordStrength(password) {
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

        // Password match checker
        function checkPasswordMatch() {
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

        // Initialize password checkers when step 3 is shown
        function initializeStep3Validators() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    checkPasswordMatch();
                });
            }
            
            if (confirmInput) {
                confirmInput.addEventListener('input', checkPasswordMatch);
            }
        }

        // Call this when DOM loads
        document.addEventListener('DOMContentLoaded', initializeStep3Validators);

        // Verify OTP and move to final step
        function verifyOTP() {
            const otpInput = document.getElementById('otp');
            const button = document.querySelector('#otp-form button[onclick="verifyOTP()"]');
            
            if (!otpInput) return;
            
            const otp = otpInput.value.trim();

            // Validation
            if (!otp || otp.length !== 6) {
                showError('otp-error', 'Please enter a valid 6-digit OTP code');
                return;
            }

            // Check if we have verified contact info
            if (!RegistrationState.verifiedEmail || !RegistrationState.verifiedPhone) {
                showError('otp-error', 'Session expired. Please start over.');
                setTimeout(() => goToStep(1), 2000);
                return;
            }

            // Disable button and show loading state
            if (button) {
                button.disabled = true;
                button.textContent = 'Verifying...';
            }
            hideError('otp-error');

            // Verify OTP with backend
            fetch('{{ route('user.verify-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ 
                    phone: RegistrationState.verifiedPhone,
                    otp: otp 
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                // Re-enable button
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Verify OTP';
                }
                
                // Check if verification successful
                if (data.success === true) {
                    // Store verified OTP and populate hidden fields
                    RegistrationState.verifiedOtp = otp;
                    
                    const emailField = document.getElementById('verified_email');
                    const phoneField = document.getElementById('verified_phone');
                    const otpField = document.getElementById('verified_otp');
                    
                    if (emailField) emailField.value = RegistrationState.verifiedEmail;
                    if (phoneField) phoneField.value = RegistrationState.verifiedPhone;
                    if (otpField) otpField.value = RegistrationState.verifiedOtp;
                    
                    // Move to step 3
                    goToStep(3);
                } else {
                    showError('otp-error', data.message || 'Invalid OTP code. Please try again.');
                }
            })
            .catch(error => {
                // Re-enable button
                if (button) {
                    button.disabled = false;
                    button.textContent = 'Verify OTP';
                }
                showError('otp-error', 'Network error. Please check your connection and try again.');
            });
        }
    </script>
</x-guest-layout>