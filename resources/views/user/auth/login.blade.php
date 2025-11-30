<x-guest>
    <x-slot:title>User Login</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">User Login</h1>
                    <p class="text-sm text-gray-600">Sign in to your account</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <x-modules.alert type="error" class="mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-modules.alert>
                @endif

                @if (session('success'))
                    <x-modules.alert type="success" class="mb-6">{{ session('success') }}</x-modules.alert>
                @endif

                @if (session('error'))
                    <x-modules.alert type="error" class="mb-6">{{ session('error') }}</x-modules.alert>
                @endif

                <!-- Login Form -->
                <form action="{{ route('user.login') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <x-modules.input type="text" name="username" label="Username" 
                        placeholder="Enter your username" required />
                    
                    <x-modules.input type="password" name="password" label="Password" 
                        placeholder="Enter your password" required />

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="form-checkbox">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
                    </div>

                    <x-modules.button type="submit" variant="primary" :fullWidth="true">
                        Sign In
                    </x-modules.button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600 space-y-2">
                    <p>
                        Don't have an account?
                        <a href="{{ route('user.register') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Register here
                        </a>
                    </p>
                    <p>
                        Forgot your password?
                        <a href="{{ route('user.forgot-password') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Reset it here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>
</x-guest>
