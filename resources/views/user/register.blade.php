<x-layout>
    <x-slot:title>User Registration</x-slot:title>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-2xl">
            <div class="card p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Account</h1>
                    <p class="text-sm text-gray-600">Join us today</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-10 h-10 mx-auto rounded-full bg-primary-600 text-white flex items-center justify-center font-semibold">1</div>
                            <p class="text-xs mt-2 font-medium text-primary-600">Contact Info</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-semibold">2</div>
                            <p class="text-xs mt-2 text-gray-500">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-semibold">3</div>
                            <p class="text-xs mt-2 text-gray-500">User Details</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Contact Information -->
                <div id="step1" class="step-content">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Step 1: Contact Information</h2>
                    <form id="contact-form" class="space-y-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                placeholder="your@email.com" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" 
                                placeholder="09123456789" required>
                        </div>

                        <div id="contact-error" class="hidden form-error"></div>

                        <button type="button" onclick="sendOTP()" class="btn btn-primary btn-block">
                            Send OTP
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="step-content hidden">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Verify OTP</h2>
                    <p class="text-sm text-gray-600 mb-6">Enter the OTP sent to your phone</p>
                    <form id="otp-form" class="space-y-6">
                        <div class="form-group">
                            <label for="otp" class="form-label">OTP Code</label>
                            <input type="text" id="otp" name="otp" class="form-input" 
                                placeholder="Enter 6-digit code" maxlength="6" required>
                        </div>

                        <div id="otp-error" class="hidden form-error"></div>

                        <div class="flex gap-3">
                            <button type="button" onclick="goToStep(1)" class="btn btn-secondary flex-1">
                                Back
                            </button>
                            <button type="button" onclick="verifyOTP()" class="btn btn-primary flex-1">
                                Verify OTP
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: User Details -->
                <div id="step3" class="step-content hidden">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Step 3: Complete Your Profile</h2>
                    
                    <!-- Notification Area -->
                    <div id="registration-notification" class="hidden mb-4"></div>
                    
                    <form action="{{ route('user.register') }}" method="POST" class="space-y-6" id="registration-form">
                        @csrf
                        <input type="hidden" id="verified_email" name="email">
                        <input type="hidden" id="verified_phone" name="phone">
                        <input type="hidden" id="verified_otp" name="otp">

                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" class="form-input" 
                                placeholder="Choose a username" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="fname" class="form-label">First Name</label>
                                <input type="text" id="fname" name="fname" class="form-input" 
                                    placeholder="First name" required>
                            </div>

                            <div class="form-group">
                                <label for="lname" class="form-label">Last Name</label>
                                <input type="text" id="lname" name="lname" class="form-input" 
                                    placeholder="Last name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-input" 
                                placeholder="Your full address" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password" class="form-input" 
                                        placeholder="Min 8 characters" required>
                                </div>
                                <!-- Password Strength Indicator -->
                                <div class="mt-2">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="password-strength-bar" class="h-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="password-strength-text" class="text-xs mt-1 text-gray-500"></p>
                                </div>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                                        placeholder="Confirm password" required>
                                </div>
                                <!-- Password Match Indicator -->
                                <p id="password-match-text" class="text-xs mt-2"></p>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="button" onclick="goToStep(2)" class="btn btn-secondary flex-1">
                                Back
                            </button>
                            <button type="submit" class="btn btn-primary flex-1" id="register-submit-btn">
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Already have an account?
                        <a href="{{ route('user.login') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Pass routes to JavaScript
        window.routes = {
            sendRegistrationOtp: '{{ route('user.send-registration-otp') }}',
            verifyOtp: '{{ route('user.verify-otp') }}'
        };
    </script>
    @vite(['resources/js/pages/user-registration.js'])
</x-layout>