<x-layout>
    <x-slot:title>Forgot Password</x-slot:title>
    
    <!-- Data attributes for JavaScript -->
    <div data-otp-sent="{{ session('success') && !session('error') ? 'true' : 'false' }}"
         data-saved-phone="{{ old('phone') }}"
         style="display: none;"></div>
    
    <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="shadow-xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h1>
                    <p class="text-gray-600" id="step-description">Enter your phone number to receive an OTP</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <x-modules.alert type="error" dismissible>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-modules.alert>
                @endif

                @if (session('success'))
                    <x-modules.alert type="success" dismissible>
                        {{ session('success') }}
                    </x-modules.alert>
                @endif

                @if (session('error'))
                    <x-modules.alert type="error" dismissible>
                        {{ session('error') }}
                    </x-modules.alert>
                @endif

                <!-- Step 1: Send OTP Form -->
                <form id="send-otp-form" action="{{ route('user.send-password-reset-otp') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Phone Number -->
                    <x-modules.input type="text" name="phone" id="phone-input" label="Phone Number"
                        placeholder="Enter your phone number (09XXXXXXXXX)" required />

                    <!-- Submit Button -->
                    <x-modules.button type="submit" variant="primary" fullWidth size="md">
                        Send OTP
                    </x-modules.button>
                </form>

                <!-- Step 2: Verify OTP Form (Hidden initially) -->
                <form id="verify-otp-form" action="{{ route('user.verify-password-reset-otp') }}" method="POST" class="space-y-5" style="display: none;">
                    @csrf

                    <!-- Phone Number (hidden) -->
                    <input type="hidden" name="phone" id="phone-hidden">

                    <!-- OTP -->
                    <x-modules.input type="text" name="otp" label="OTP Code"
                        placeholder="Enter 6-digit OTP" required maxlength="6" />

                    <!-- Submit Button -->
                    <x-modules.button type="submit" variant="primary" fullWidth size="md">
                        Verify OTP
                    </x-modules.button>

                    <!-- Resend OTP -->
                    <div class="text-center">
                        <button type="button" id="resend-otp" class="text-green-600 hover:text-green-700 font-medium text-sm">
                            Resend OTP
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Remember your password?
                        <a href="{{ route('user.login') }}"
                            class="text-green-600 hover:text-green-700 font-medium">
                            Sign in here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>

</x-layout>
