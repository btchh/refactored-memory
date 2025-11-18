<x-layout>
    <x-slot:title>Change Password</x-slot:title>

    <x-nav type="admin" />

    <div class="container mx-auto px-4 py-8">
        <div class="page-header mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Change Password</h1>
        <p class="text-gray-600 mt-2">Update your account password</p>
    </div>

    <div class="max-w-lg">
        <x-modules.card title="Update Your Password">
            <form action="{{ route('admin.update-password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <x-modules.input 
                    type="password" 
                    name="current_password" 
                    label="Current Password" 
                    placeholder="Enter your current password"
                    required 
                />

                <x-modules.input 
                    type="password" 
                    name="new_password" 
                    label="New Password" 
                    placeholder="Enter new password (min 8 characters)"
                    required 
                />

                <x-modules.input 
                    type="password" 
                    name="new_password_confirmation" 
                    label="Confirm New Password" 
                    placeholder="Confirm your new password"
                    required 
                />

                <div class="flex gap-3 pt-4">
                    <x-modules.button type="submit" variant="primary" class="flex-1">
                        Update Password
                    </x-modules.button>
                    <a href="{{ route('admin.dashboard') }}" class="flex-1">
                        <x-modules.button type="button" variant="secondary" fullWidth>
                            Cancel
                        </x-modules.button>
                    </a>
                </div>
            </form>
        </x-modules.card>
        </div>
    </div>
</x-layout>
