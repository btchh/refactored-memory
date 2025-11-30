<x-guest>
    <x-slot:title>Reset Password - Admin</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <x-modules.card class="p-8">
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

                @if(!isset($token))
                    <!-- Step 1: Request Reset Link -->
                    <form action="{{ route('admin.send-password-reset') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <x-modules.input type="email" name="email" label="Email Address" 
                            placeholder="Enter your registered email" required />

                        <x-modules.button type="submit" variant="primary" :fullWidth="true">
                            Send Reset Link
                        </x-modules.button>
                    </form>
                @else
                    <!-- Step 2: Reset Password -->
                    <form action="{{ route('admin.reset-password') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <x-modules.input type="email" name="email" label="Email Address" 
                            placeholder="Enter your email" required />
                        
                        <x-modules.input type="password" name="password" label="New Password" 
                            placeholder="Enter new password (min 8 characters)" required />
                        
                        <x-modules.input type="password" name="password_confirmation" label="Confirm Password" 
                            placeholder="Confirm your new password" required />

                        <x-modules.button type="submit" variant="primary" :fullWidth="true">
                            Reset Password
                        </x-modules.button>
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
            </x-modules.card>
        </div>
    </div>
</x-guest>
