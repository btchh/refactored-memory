<x-guest :showNav="true">
    <x-slot:title>Reset Password</x-slot:title>
    
    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 pt-20">
        <div class="w-full max-w-md">
            <x-modules.card class="p-8">
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

                <!-- Validation Errors -->
                @if ($errors->any())
                    <x-modules.alert type="error" class="mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-modules.alert>
                @endif

                @if(!isset($phone))
                    <!-- Step 1: Request OTP -->
                    <form action="{{ route('user.send-password-reset-otp') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <x-modules.input type="text" name="phone" label="Phone Number" 
                            placeholder="Enter your phone number (09XXXXXXXXX)" required />

                        <x-modules.button type="submit" variant="primary" :fullWidth="true">
                            Send OTP
                        </x-modules.button>
                    </form>
                @else
                    <!-- Step 2: Verify OTP & Reset Password -->
                    <form action="{{ route('user.reset-password') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="phone" value="{{ $phone }}">

                        <x-modules.input type="text" name="otp" label="OTP Code" 
                            placeholder="Enter 6-digit OTP" required />
                        
                        <x-modules.input type="password" name="password" label="New Password" 
                            placeholder="Enter your new password" required />
                        
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
                        <a href="{{ route('user.login') }}" class="text-primary-600 hover:text-primary-700 font-medium hover:underline">
                            Sign in here
                        </a>
                    </p>
                </div>
            </x-modules.card>
        </div>
    </div>
</x-guest>
