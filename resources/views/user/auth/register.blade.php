<x-guest :showNav="true">
    <x-slot:title>User Registration</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 pt-20">
        <div class="w-full max-w-2xl">
            <!-- Content Card -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-8 shadow-sm">
                <!-- Header with Wash Accent -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-wash/10 rounded-xl mb-4">
                        <svg class="w-8 h-8 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 mb-2">Create Account</h1>
                    <p class="text-sm text-gray-600">Join us today</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-10 h-10 mx-auto rounded-full bg-wash text-white flex items-center justify-center font-bold">1</div>
                            <p class="text-xs mt-2 font-bold text-wash">Contact Info</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                            <p class="text-xs mt-2 text-gray-500 font-medium">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                            <p class="text-xs mt-2 text-gray-500 font-medium">User Details</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Contact Information -->
                <div id="step1" class="step-content">
                    <h2 class="text-lg font-black text-gray-900 mb-6">Step 1: Contact Information</h2>
                    <form id="contact-form" class="space-y-6">
                        <!-- Email Input -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address <span class="text-error">*</span></label>
                            <input type="email" id="email" name="email" class="form-input" 
                                placeholder="your@email.com" required>
                        </div>
                        
                        <!-- Phone Input -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number <span class="text-error">*</span></label>
                            <input type="tel" id="phone" name="phone" class="form-input" 
                                placeholder="09123456789" required>
                        </div>

                        <div id="contact-error" class="hidden"></div>

                        <!-- Submit Button -->
                        <button type="button" class="btn btn-primary btn-lg w-full" onclick="sendOTP()">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Send OTP
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="step-content hidden">
                    <h2 class="text-lg font-black text-gray-900 mb-4">Step 2: Verify OTP</h2>
                    <p class="text-sm text-gray-600 mb-6">Enter the OTP sent to your phone</p>
                    <form id="otp-form" class="space-y-6">
                        <!-- OTP Input -->
                        <div class="form-group">
                            <label for="otp" class="form-label">OTP Code <span class="text-error">*</span></label>
                            <input type="text" id="otp" name="otp" class="form-input" 
                                placeholder="Enter 6-digit code" required>
                        </div>

                        <div id="otp-error" class="hidden"></div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button type="button" class="btn btn-outline flex-1" onclick="goToStep(1)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back
                            </button>
                            <button type="button" class="btn btn-primary flex-1" onclick="verifyOTP()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verify OTP
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: User Details -->
                <div id="step3" class="step-content hidden">
                    <h2 class="text-lg font-black text-gray-900 mb-6">Step 3: Complete Your Profile</h2>
                    
                    <div id="registration-notification" class="hidden mb-4"></div>
                    
                    <form action="{{ route('user.register') }}" method="POST" class="space-y-6" id="registration-form">
                        @csrf
                        <input type="hidden" id="verified_email" name="email">
                        <input type="hidden" id="verified_phone" name="phone">
                        <input type="hidden" id="verified_otp" name="otp">

                        <!-- Username Input -->
                        <div class="form-group">
                            <label for="username" class="form-label">Username <span class="text-error">*</span></label>
                            <input type="text" id="username" name="username" class="form-input" 
                                placeholder="Choose a username" required>
                        </div>

                        <!-- Name Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="fname" class="form-label">First Name <span class="text-error">*</span></label>
                                <input type="text" id="fname" name="fname" class="form-input" 
                                    placeholder="First name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="lname" class="form-label">Last Name <span class="text-error">*</span></label>
                                <input type="text" id="lname" name="lname" class="form-input" 
                                    placeholder="Last name" required>
                            </div>
                        </div>

                        <!-- Address Input -->
                        <div class="form-group">
                            <label for="address" class="form-label">Address <span class="text-error">*</span></label>
                            <input type="text" id="address" name="address" class="form-input" 
                                placeholder="Your full address" required>
                        </div>

                        <!-- Password Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Password <span class="text-error">*</span></label>
                                    <input type="password" id="password" name="password" class="form-input" 
                                        placeholder="Min 8 characters" required>
                                </div>
                                <div class="mt-2">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="password-strength-bar" class="h-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="password-strength-text" class="text-xs mt-1 text-gray-500"></p>
                                </div>
                            </div>

                            <div>
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label">Confirm Password <span class="text-error">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                                        placeholder="Confirm password" required>
                                </div>
                                <p id="password-match-text" class="text-xs mt-2"></p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 pt-4">
                            <button type="button" class="btn btn-outline flex-1" onclick="goToStep(2)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Back
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg flex-1" id="register-submit-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Already have an account?
                        <a href="{{ route('user.login') }}" class="text-wash hover:text-wash-dark font-bold hover:underline transition-colors">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.routes = {
            sendRegistrationOtp: '{{ route('user.send-registration-otp') }}',
            verifyOtp: '{{ route('user.verify-otp') }}'
        };
    </script>
    @vite(['resources/js/pages/user-registration.js'])
</x-guest>
