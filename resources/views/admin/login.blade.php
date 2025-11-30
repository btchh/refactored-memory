<x-layout>
    <x-slot:title>Admin Login</x-slot:title>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="card p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Admin Portal</h1>
                    <p class="text-sm text-gray-600">Sign in to your account</p>
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

                @if (session('error'))
                    <x-modules.alert type="error" dismissible>
                        {{ session('error') }}
                    </x-modules.alert>
                @endif

                <!-- Login Form -->
                <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Admin Name / Email -->
                    <div class="form-group">
                        <label for="admin_name" class="form-label">Admin Name or Email</label>
                        <input type="text" id="admin_name" name="admin_name" class="form-input" 
                            placeholder="Enter your admin name or email" required>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-input" 
                            placeholder="Enter your password" required>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                            class="form-checkbox">
                        <label for="remember" class="form-check-label">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block">
                        Sign In
                    </button>
                </form>

                <!-- Footer -->
                <div class="mt-6 text-center text-sm text-gray-600">
                    <p>
                        Forgot your password?
                        <a href="{{ route('admin.forgot-password') }}"
                            class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Reset it here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
