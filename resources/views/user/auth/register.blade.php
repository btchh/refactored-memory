<x-guest :showNav="true">
    <x-slot:title>User Registration</x-slot:title>
    
    <div class="min-h-screen flex items-center justify-center p-4 pt-20 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-center bg-no-repeat" style="background-image: url('{{ asset('images/image.png') }}'); background-size: cover; background-position: center center; filter: blur(3px); transform: scale(1.1);">
            <!-- Overlay for better readability -->
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/15 via-blue-900/15 to-pink-900/15"></div>
        </div>
        
        <!-- Fallback gradient if no image -->
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 via-white to-blue-50 -z-10"></div>
        
        <!-- Animated Background Elements (optional decorative elements) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 -right-40 w-96 h-96 bg-gradient-to-br from-purple-400/10 to-blue-400/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-blue-400/10 to-pink-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1.5s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-pink-400/5 to-purple-400/5 rounded-full blur-3xl animate-pulse" style="animation-delay: 0.75s;"></div>
        </div>

        <div class="w-full max-w-2xl relative z-10">
            <x-modules.card class="p-8 bg-white/95 shadow-2xl border-0 card-entrance rounded-2xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h1>
                    <p class="text-sm text-gray-600">Join us today</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-10">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-lg transition-all duration-300">1</div>
                            <p class="text-xs mt-2 font-semibold text-blue-600">Contact Info</p>
                        </div>
                        <div class="flex-1 h-1.5 bg-gray-200 rounded-full mx-2 transition-all duration-500" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold transition-all duration-300">2</div>
                            <p class="text-xs mt-2 text-gray-500 font-medium">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1.5 bg-gray-200 rounded-full mx-2 transition-all duration-500" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold transition-all duration-300">3</div>
                            <p class="text-xs mt-2 text-gray-500 font-medium">User Details</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Contact Information -->
                <div id="step1" class="step-content">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-5 mb-6 border border-blue-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg text-white text-sm font-bold">1</span>
                            Contact Information
                        </h2>
                        <p class="text-sm text-gray-600 ml-9">We'll send you a verification code</p>
                    </div>
                    <form id="contact-form" class="space-y-6">
                        <x-modules.input type="email" name="email" label="Email Address" 
                            placeholder="your@email.com" required />
                        
                        <x-modules.input type="tel" name="phone" label="Phone Number" 
                            placeholder="09123456789" required />

                        <div id="contact-error" class="hidden"></div>

                        <x-modules.button type="button" variant="primary" :fullWidth="true" onclick="sendOTP()" class="shadow-lg hover:shadow-xl transition-all duration-200">
                            Send OTP
                        </x-modules.button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="step-content hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-5 mb-6 border border-purple-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 bg-gradient-to-br from-purple-500 to-blue-600 rounded-lg text-white text-sm font-bold">2</span>
                            Verify OTP
                        </h2>
                        <p class="text-sm text-gray-600 ml-9">Enter the OTP sent to your phone</p>
                    </div>
                    <form id="otp-form" class="space-y-6">
                        <x-modules.input type="text" name="otp" label="OTP Code" 
                            placeholder="Enter 6-digit code" required />

                        <div id="otp-error" class="hidden"></div>

                        <div class="flex gap-3">
                            <x-modules.button type="button" variant="outline" onclick="goToStep(1)" class="flex-1">
                                Back
                            </x-modules.button>
                            <x-modules.button type="button" variant="primary" onclick="verifyOTP()" class="flex-1 shadow-lg hover:shadow-xl transition-all duration-200">
                                Verify OTP
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: User Details -->
                <div id="step3" class="step-content hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-5 mb-6 border border-blue-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg text-white text-sm font-bold">3</span>
                            Complete Your Profile
                        </h2>
                        <p class="text-sm text-gray-600 ml-9">Just a few more details</p>
                    </div>
                    
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
                            <x-modules.button type="submit" variant="primary" class="flex-1 shadow-lg hover:shadow-xl transition-all duration-200" id="register-submit-btn">
                                Create Account
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white text-gray-500 uppercase tracking-wider">Already a member?</span>
                    </div>
                </div>

                <!-- Footer -->
                <div>
                    <a href="{{ route('user.login') }}" class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-gray-700 font-medium hover:border-purple-400 hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign In to Existing Account
                    </a>
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
