<x-layout>
    <x-slot:title>Verify OTP & Reset Password</x-slot:title>
    <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="shadow-xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Verify OTP</h1>
                    <p class="text-gray-600">Enter the OTP and your new password</p>
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

                <!-- OTP Verification Form -->
                <form action="{{ route('user.reset-password') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Phone Number (hidden) -->
                    <input type="hidden" name="phone" value="{{ $phone }}">

                    <!-- OTP -->
                    <x-modules.input type="text" name="otp" label="OTP Code"
                        placeholder="Enter 6-digit OTP" required />

                    <!-- New Password -->
                    <x-modules.input type="password" name="password" label="New Password"
                        placeholder="Enter your new password" required />

                    <!-- Confirm Password -->
                    <x-modules.input type="password" name="password_confirmation" label="Confirm Password"
                        placeholder="Confirm your new password" required />

                    <!-- Submit Button -->
                    <x-modules.button type="submit" variant="primary" fullWidth size="md">
                        Reset Password
                    </x-modules.button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        <a href="{{ route('user.login') }}"
                            class="text-green-600 hover:text-green-700 font-medium">
                            Back to login
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>
</x-layout>
