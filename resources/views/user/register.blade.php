<x-layout>
    <x-slot:title>User Registration</x-slot:title>
    <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="shadow-xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
                    <p class="text-gray-600">Join us today</p>
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

                <!-- Registration Form -->
                <form action="{{ route('user.register') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- OTP -->
                    <x-modules.input type="text" name="otp" label="OTP Code"
                        placeholder="Enter 6-digit OTP" required />

                    <!-- Username -->
                    <x-modules.input type="text" name="username" label="Username"
                        placeholder="Choose a username" required />

                    <!-- First Name -->
                    <x-modules.input type="text" name="fname" label="First Name"
                        placeholder="Enter your first name" required />

                    <!-- Last Name -->
                    <x-modules.input type="text" name="lname" label="Last Name"
                        placeholder="Enter your last name" required />

                    <!-- Address -->
                    <x-modules.input type="text" name="address" label="Address"
                        placeholder="Enter your address" required />

                    <!-- Password -->
                    <x-modules.input type="password" name="password" label="Password"
                        placeholder="Enter a strong password" required />

                    <!-- Confirm Password -->
                    <x-modules.input type="password" name="password_confirmation" label="Confirm Password"
                        placeholder="Confirm your password" required />

                    <!-- Submit Button -->
                    <x-modules.button type="submit" variant="primary" fullWidth size="md">
                        Create Account
                    </x-modules.button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Already have an account?
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
