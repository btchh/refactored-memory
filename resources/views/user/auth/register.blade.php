<x-layout>
    <x-slot:title>User Registration</x-slot:title>
    
    <!-- Data attributes for JavaScript -->
    <div data-send-otp-url="{{ route('user.send-registration-otp') }}"
         data-verify-otp-url="{{ route('user.verify-otp') }}"
         data-register-url="{{ route('user.register') }}"
         style="display: none;"></div>
    
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

                        <x-modules.button type="button" id="send-otp-btn" onclick="sendOTP()" variant="primary" fullWidth>
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
                            <x-modules.button type="button" id="verify-otp-btn" onclick="verifyOTP()" variant="primary" class="flex-1">
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

</x-layout>
