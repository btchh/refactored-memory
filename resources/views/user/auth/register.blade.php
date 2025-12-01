<x-guest :showNav="true">
    <x-slot:title>User Registration</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 pt-20">
        <div class="w-full max-w-2xl">
            <x-modules.card class="p-8">
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
                        <x-modules.input type="email" name="email" label="Email Address" 
                            placeholder="your@email.com" required />
                        
                        <x-modules.input type="tel" name="phone" label="Phone Number" 
                            placeholder="09123456789" required />

                        <div id="contact-error" class="hidden"></div>

                        <x-modules.button type="button" variant="primary" :fullWidth="true" onclick="sendOTP()">
                            Send OTP
                        </x-modules.button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="step-content hidden">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Step 2: Verify OTP</h2>
                    <p class="text-sm text-gray-600 mb-6">Enter the OTP sent to your phone</p>
                    <form id="otp-form" class="space-y-6">
                        <x-modules.input type="text" name="otp" label="OTP Code" 
                            placeholder="Enter 6-digit code" required />

                        <div id="otp-error" class="hidden"></div>

                        <div class="flex gap-3">
                            <x-modules.button type="button" variant="outline" onclick="goToStep(1)" class="flex-1">
                                Back
                            </x-modules.button>
                            <x-modules.button type="button" variant="primary" onclick="verifyOTP()" class="flex-1">
                                Verify OTP
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: User Details -->
                <div id="step3" class="step-content hidden">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Step 3: Complete Your Profile</h2>
                    
                    <div id="registration-notification" class="hidden mb-4"></div>
                    
                    <form action="{{ route('user.register') }}" method="POST" class="space-y-6" id="registration-form">
                        @csrf
                        <input type="hidden" id="verified_email" name="email">
                        <input type="hidden" id="verified_phone" name="phone">
                        <input type="hidden" id="verified_otp" name="otp">

                        <x-modules.input type="text" name="username" label="Username" 
                            placeholder="Choose a username" required />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-modules.input type="text" name="fname" label="First Name" 
                                placeholder="First name" required />
                            
                            <x-modules.input type="text" name="lname" label="Last Name" 
                                placeholder="Last name" required />
                        </div>

                        <x-modules.input type="text" name="address" label="Address" 
                            placeholder="Your full address" required />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-modules.input type="password" name="password" label="Password" 
                                    placeholder="Min 8 characters" required />
                                <div class="mt-2">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="password-strength-bar" class="h-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                    <p id="password-strength-text" class="text-xs mt-1 text-gray-500"></p>
                                </div>
                            </div>

                            <div>
                                <x-modules.input type="password" name="password_confirmation" label="Confirm Password" 
                                    placeholder="Confirm password" required />
                                <p id="password-match-text" class="text-xs mt-2"></p>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <x-modules.button type="button" variant="outline" onclick="goToStep(2)" class="flex-1">
                                Back
                            </x-modules.button>
                            <x-modules.button type="submit" variant="primary" class="flex-1" id="register-submit-btn">
                                Create Account
                            </x-modules.button>
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
            </x-modules.card>
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
