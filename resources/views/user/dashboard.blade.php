<x-layout>
    <x-slot:title>Dashboard</x-slot:title>
    <x-nav type="user" />
    <div class="min-h-screen bg-gray-100">

        <!-- Main Content -->
        <main class="container mx-auto p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 my-8">
                <x-modules.card>
                    <h3 class="text-lg font-bold mb-2">Welcome</h3>
                    <p class="text-gray-600">Welcome to your dashboard, {{ Auth::user()->fname }}!</p>
                </x-modules.card>

                <x-modules.card>
                    <h3 class="text-lg font-bold mb-2">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('user.profile') }}" class="text-green-600 hover:underline">View Profile</a></li>
                        <li><a href="{{ route('user.change-password') }}" class="text-green-600 hover:underline">Change Password</a></li>
                    </ul>
                </x-modules.card>

                <x-modules.card>
                    <h3 class="text-lg font-bold mb-2">Account Info</h3>
                    <p class="text-sm text-gray-600">
                        <strong>Username:</strong> {{ Auth::user()->username }}<br>
                        <strong>Email:</strong> {{ Auth::user()->email }}
                    </p>
                </x-modules.card>
            </div>
        </main>
    </div>
</x-layout>
