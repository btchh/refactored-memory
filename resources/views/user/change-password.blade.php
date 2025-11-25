<x-layout>
    <x-slot:title>Change Password</x-slot:title>
    <x-nav type="user" />

    <!-- ✅ Add top padding so content sits below nav -->
    <div class="min-h-screen bg-gray-100 pt-[7rem] pb-12">

        <!-- Main Content -->
        <main class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto my-8">
                <x-modules.card>
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

                    <!-- ✅ Centered Title -->
                    <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Change Password</h2>

                    <!-- Change Password Form -->
                    <form action="{{ route('user.change-password') }}" method="POST" class="space-y-5">
                        @csrf

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
                            Change Password
                        </x-modules.button>
                    </form>
                </x-modules.card>
            </div>
        </main>
    </div>
</x-layout>
