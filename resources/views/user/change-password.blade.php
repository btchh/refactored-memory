<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-2xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-50 rounded-full mb-4">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Change Password
                </h1>
                <p class="text-gray-600">Keep your account secure with a strong password</p>
            </div>

            <x-modules.card class="p-6 md:p-8">
                    <!-- Alert Messages -->
                    @if (isset($errors) && $errors->any())
                        <x-modules.alert type="error" dismissible class="mb-4">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-modules.alert>
                    @endif

                    @if (session('success'))
                        <x-modules.alert type="success" dismissible class="mb-4">
                            {{ session('success') }}
                        </x-modules.alert>
                    @endif

                    <!-- Change Password Form -->
                    <form action="{{ route('user.change-password') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Password Strength Info -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-blue-900 mb-1">Password Requirements</h3>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li>• Minimum 8 characters</li>
                                        <li>• Mix of uppercase and lowercase letters</li>
                                        <li>• Include numbers and special characters</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Current Password -->
                        <x-modules.input type="password" name="current_password" label="Current Password"
                            placeholder="Enter your current password" required />

                        <!-- New Password -->
                        <x-modules.input type="password" name="new_password" label="New Password"
                            placeholder="Enter your new password" required />

                        <!-- Confirm New Password -->
                        <x-modules.input type="password" name="new_password_confirmation" label="Confirm New Password"
                            placeholder="Confirm your new password" required />

                        <!-- Submit Button -->
                        <x-modules.button type="submit" variant="primary" fullWidth size="md">
                            Update Password
                        </x-modules.button>
                    </form>
                </x-modules.card>
        </div>
    </div>
</x-layout>
