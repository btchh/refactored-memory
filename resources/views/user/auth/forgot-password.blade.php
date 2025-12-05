<x-guest :showNav="true">
    <x-slot:title>Reset Password</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 pt-20">
        <div class="w-full max-w-2xl">
            <x-modules.card class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-sm text-gray-600">Recover your account</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-10 h-10 mx-auto rounded-full bg-primary-600 text-white flex items-center justify-center font-semibold">1</div>
                            <p class="text-xs mt-2 font-medium text-primary-600">Phone Number</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-semibold">2</div>
                            <p class="text-xs mt-2 text-gray-500">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-semibold">3</div>
                            <p class="text-xs mt-2 text-gray-500">New Password</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Phone Number -->
                <div id="step1" class="step-content">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Step 1: Enter Phone Number</h2>
                    <form id="phone-form" class="space-y-6">
                        <x-modules.input type="tel" name="phone" label="Phone Number" 
                            placeholder="09123456789" required />

                        <div id="phone-error" class="hidden"></div>

                        <x-modules.button type="button" variant="primary" :fullWidth="true" onclick="sendResetOTP()">
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
                            <x-modules.button type="button" variant="primary" onclick="verifyResetOTP()" class="flex-1">
                                Verify OTP
                            </x-modules.button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: New Password -->
                <div id="step3" class="step-content hidden">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Step 3: Set New Password</h2>
                    
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
                            <x-modules.button type="submit" variant="primary" class="flex-1" id="reset-submit-btn">
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
