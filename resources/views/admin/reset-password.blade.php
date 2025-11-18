<x-layout>
    <x-slot:title>Reset Password - Admin</x-slot:title>
    <div class="min-h-screen bg-gradient-to-br from-blue-600 to-blue-800 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="shadow-xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h1>
                    <p class="text-gray-600">Enter your new password below</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <x-modules.alert type="error" dismissible class="mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-modules.alert>
                @endif

                @if (session('success'))
                    <x-modules.alert type="success" dismissible class="mb-6">
                        {{ session('success') }}
                    </x-modules.alert>
                @endif

                <!-- Reset Form -->
                <form action="{{ route('admin.reset-password') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Token -->
                    <input type="hidden" name="token" value="{{ $token ?? '' }}">

                    <!-- Email -->
                    <x-modules.input type="email" name="email" label="Email Address" placeholder="Enter your email"
                        required />

                    <!-- Password -->
                    <x-modules.input type="password" name="password" label="New Password"
                        placeholder="Enter new password (min 8 characters)" required />

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
                        Back to login?
                        <a href="{{ route('admin.login') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            Sign in here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>
</x-layout>
