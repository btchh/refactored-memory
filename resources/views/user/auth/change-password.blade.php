<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-2xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-wash/10 rounded-xl mb-4">
                    <svg class="w-10 h-10 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-black text-gray-900 mb-2">
                    Change Password
                </h1>
                <p class="text-gray-600">Keep your account secure with a strong password</p>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6 md:p-8 shadow-sm">
                <!-- Validation Errors -->
                @if (isset($errors) && $errors->any())
                    <div class="alert alert-error mb-6">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Change Password Form -->
                <form action="{{ route('user.change-password') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Password Strength Info -->
                    <div class="alert alert-info">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-bold mb-1">Password Requirements</h3>
                            <ul class="text-sm space-y-1">
                                <li>• Minimum 8 characters</li>
                                <li>• Mix of uppercase and lowercase letters</li>
                                <li>• Include numbers and special characters</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password <span class="text-error">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="form-input"
                            placeholder="Enter your current password" required>
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="new_password" class="form-label">New Password <span class="text-error">*</span></label>
                        <input type="password" id="new_password" name="new_password" class="form-input"
                            placeholder="Enter your new password" required>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="form-group">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-error">*</span></label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-input"
                            placeholder="Confirm your new password" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layout>
