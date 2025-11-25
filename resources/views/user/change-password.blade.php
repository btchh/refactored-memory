<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-3xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-orange-500 to-red-500 rounded-full mb-4 shadow-lg">
                    <span class="text-5xl">🔒</span>
                </div>
                <h1 class="text-5xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-3">
                    Change Password
                </h1>
                <p class="text-gray-600 text-lg">Keep your account secure with a strong password</p>
            </div>

            <x-modules.card class="p-8 md:p-10 shadow-2xl border-2 border-gray-100"
                    <!-- Alert Messages -->
                    @if ($errors->any())
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
                                <span class="text-2xl">💡</span>
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
                        <div class="space-y-2">
                            <x-modules.input type="password" name="current_password" label="Current Password"
                                placeholder="Enter your current password" required class="text-lg py-3" />
                        </div>

                        <!-- New Password -->
                        <div class="space-y-2">
                            <x-modules.input type="password" name="new_password" label="New Password"
                                placeholder="Enter your new password" required class="text-lg py-3" />
                        </div>

                        <!-- Confirm New Password -->
                        <div class="space-y-2">
                            <x-modules.input type="password" name="new_password_confirmation" label="Confirm New Password"
                                placeholder="Confirm your new password" required class="text-lg py-3" />
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-4">
                            <x-modules.button type="submit" variant="primary" fullWidth size="lg" class="bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 transition-all duration-300 hover:scale-105 text-lg py-4">
                                🔐 Update Password
                            </x-modules.button>
                        </div>
                    </form>
                </x-modules.card>
        </div>
    </div>
</x-layout>
