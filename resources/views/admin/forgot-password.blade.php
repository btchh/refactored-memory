<x-layout>
    <x-slot:title>Reset Password - Admin</x-slot:title>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="card p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                    <p class="text-sm text-gray-600">
                        @if(!isset($token))
                            Enter your email to receive a password reset link
                        @else
                            Enter your new password below
                        @endif
                    </p>
                </div>

                <!-- Alert Messages -->
                @if (isset($errors) && $errors->any())
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

                @if(!isset($token))
                    <!-- Step 1: Request Reset Link -->
                    <form action="{{ route('admin.send-password-reset') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                placeholder="Enter your registered email" required>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block">
                            Send Reset Link
                        </button>
                    </form>
                @else
                    <!-- Step 2: Reset Password -->
                    <form action="{{ route('admin.reset-password') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Token -->
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" 
                                placeholder="Enter your email" required>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password" class="form-input" 
                                placeholder="Enter new password (min 8 characters)" required>
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
                        <a href="{{ route('admin.login') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layout>
