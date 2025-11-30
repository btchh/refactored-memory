<x-layout>
    <x-slot:title>Reset Password</x-slot:title>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="card p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-sm text-gray-600">
                        @if(!isset($phone))
                            Enter your phone number to receive an OTP
                        @else
                            Enter the OTP and your new password
                        @endif
                    </p>
                </div>

                <!-- Alert Messages -->
                @if (isset($errors) && $errors->any())
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

                @if(!isset($phone))
                    <!-- Step 1: Request OTP -->
                    <form action="{{ route('user.send-password-reset-otp') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="form-input" 
                                placeholder="Enter your phone number (09XXXXXXXXX)" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block">
                            Send OTP
                        </button>
                    </form>
                @else
                    <!-- Step 2: Verify OTP & Reset Password -->
                    <form action="{{ route('user.reset-password') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Phone Number (hidden) -->
                        <input type="hidden" name="phone" value="{{ $phone }}">

                        <!-- OTP -->
                        <div class="form-group">
                            <label for="otp" class="form-label">OTP Code</label>
                            <input type="text" id="otp" name="otp" class="form-input" 
                                placeholder="Enter 6-digit OTP" required>
                        </div>

                        <!-- New Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-input" 
                                placeholder="Enter your new password" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" 
                                placeholder="Confirm your new password" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block">
                            Reset Password
                        </button>
                    </form>
                @endif

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Remember your password?
                        <a href="{{ route('user.login') }}"
                            class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
