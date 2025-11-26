<x-guest-layout>
    <x-slot:title>User Login</x-slot:title>
    <div class="min-h-screen bg-gradient-to-br from-green-600 to-green-800 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="shadow-xl">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">User Login</h1>
                    <p class="text-gray-600">Sign in to your account</p>
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

                <!-- Login Form -->
                <form action="{{ route('user.login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Username -->
                    <x-modules.input type="text" name="username" label="Username"
                        placeholder="Enter your username" required />

                    <!-- Password -->
                    <x-modules.input type="password" name="password" label="Password" placeholder="Enter your password"
                        required />

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <x-modules.button type="submit" variant="primary" fullWidth size="md">
                        Sign In
                    </x-modules.button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600 space-y-3">
                    <p>
                        Don't have an account?
                        <a href="{{ route('user.register') }}"
                            class="text-green-600 hover:text-green-700 font-medium">
                            Register here
                        </a>
                    </p>
                    <p>
                        Forgot your password?
                        <a href="{{ route('user.forgot-password') }}"
                            class="text-green-600 hover:text-green-700 font-medium">
                            Reset it here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>
</x-guest-layout>
