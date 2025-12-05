<x-guest :showNav="true">
    <x-slot:title>Reset Password</x-slot:title>
    
    <div class="min-h-screen flex items-center justify-center p-4 pt-20 relative overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 bg-center bg-no-repeat" style="background-image: url('{{ asset('images/image.png') }}'); background-size: cover; background-position: center center; filter: blur(3px); transform: scale(1.1);">
            <!-- Overlay for better readability -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/15 via-purple-900/15 to-blue-900/15"></div>
        </div>
        
        <!-- Fallback gradient if no image -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50 -z-10"></div>
        
        <!-- Animated Background Elements (optional decorative elements) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-indigo-400/10 to-purple-400/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-tr from-purple-400/10 to-blue-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        </div>

        <div class="w-full max-w-2xl relative z-10">
            <x-modules.card class="p-8 bg-white/95 shadow-2xl border-0 card-entrance rounded-2xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-sm text-gray-600">Recover your account</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-10">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-lg transition-all duration-300">1</div>
                            <p class="text-xs mt-2 font-semibold text-indigo-600">Phone Number</p>
                        </div>
                        <div class="flex-1 h-1.5 bg-gray-200 rounded-full mx-2 transition-all duration-500" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold transition-all duration-300">2</div>
                            <p class="text-xs mt-2 text-gray-500 font-medium">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1.5 bg-gray-200 rounded-full mx-2 transition-all duration-500" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-200 text-gray-500 flex items-center justify-center font-bold transition-all duration-300">3</div>
                            <p class="text-xs mt-2 text-gray-500 font-medium">New Password</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Phone Number -->
                <div id="step1" class="step-content">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-5 mb-6 border border-indigo-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg text-white text-sm font-bold">1</span>
                            Enter Phone Number
                        </h2>
                        <p class="text-sm text-gray-600 ml-9">We'll send you a verification code</p>
                    </div>
                    <form id="phone-form" class="space-y-6">
                        <x-modules.input type="tel" name="phone" label="Phone Number" 
                            placeholder="09123456789" required />

                        <div id="phone-error" class="hidden"></div>

                        <x-modules.button type="button" variant="primary" :fullWidth="true" onclick="sendResetOTP()" class="shadow-lg hover:shadow-xl transition-all duration-200">
                            Send OTP
                        </x-modules.button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification -->
                <div id="step2" class="step-content hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-5 mb-6 border border-purple-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg text-white text-sm font-bold">2</span>
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
                            <x-modules.button type="button" variant="primary" onclick="verifyResetOTP()" class="flex-1 shadow-lg hover:shadow-xl transition-all duration-200">
                                Verify OTP
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: New Password -->
                <div id="step3" class="step-content hidden">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-5 mb-6 border border-indigo-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                            <span class="flex items-center justify-center w-7 h-7 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg text-white text-sm font-bold">3</span>
                            Set New Password
                        </h2>
                        <p class="text-sm text-gray-600 ml-9">Choose a strong password</p>
                    </div>
                    
                    <div id="reset-notification" class="hidden mb-4"></div>
                    
                    <form action="{{ route('user.reset-password.submit') }}" method="POST" class="space-y-6" id="reset-form">
                        @csrf
                        <input type="hidden" id="verified_phone" name="phone">
                        <input type="hidden" id="verified_otp" name="otp">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-modules.input type="password" name="password" label="New Password" 
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
                            <x-modules.button type="submit" variant="primary" class="flex-1 shadow-lg hover:shadow-xl transition-all duration-200" id="reset-submit-btn">
                                Reset Password
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Remember your password?
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
            sendPasswordResetOtp: '{{ route('user.send-password-reset-otp') }}',
            verifyPasswordResetOtp: '{{ route('user.verify-password-reset-otp') }}'
        };
    </script>
    @vite(['resources/js/pages/user-password-reset.js'])
</x-guest>
