<x-layout>
    <x-slot:title>Admin Dashboard</x-slot:title>

    <x-nav type="admin" />

    <div class="container mx-auto px-4 py-8">
        <!-- Notifications -->
        <x-notifications />

            <!-- Validation Errors -->
            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="page-header mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome, <strong>{{ Auth::guard('admin')->user()->admin_name }}</strong></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <x-modules.card class="bg-gradient-to-br from-purple-600 to-purple-800 text-white">
                    <div class="text-sm opacity-90">Total Users</div>
                    <div class="text-4xl font-bold mt-2">0</div>
                </x-modules.card>

                <x-modules.card class="bg-gradient-to-br from-pink-500 to-red-500 text-white">
                    <div class="text-sm opacity-90">Active Sessions</div>
                    <div class="text-4xl font-bold mt-2">0</div>
                </x-modules.card>

                <x-modules.card class="bg-gradient-to-br from-blue-400 to-cyan-400 text-white">
                    <div class="text-sm opacity-90">Transactions</div>
                    <div class="text-4xl font-bold mt-2">0</div>
                </x-modules.card>

                <x-modules.card class="bg-gradient-to-br from-green-400 to-teal-400 text-white">
                    <div class="text-sm opacity-90">System Status</div>
                    <div class="text-4xl font-bold mt-2">Online</div>
                </x-modules.card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-modules.card title="Admin Profile">
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Admin Name</div>
                            <div class="font-semibold text-gray-800">{{ Auth::guard('admin')->user()->admin_name }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Email</div>
                            <div class="font-semibold text-gray-800">{{ Auth::guard('admin')->user()->email }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 mb-1">Phone</div>
                            <div class="font-semibold text-gray-800">{{ Auth::guard('admin')->user()->phone }}</div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.profile') }}">
                                <x-modules.button variant="primary" fullWidth>View Profile</x-modules.button>
                            </a>
                        </div>
                    </div>
                </x-modules.card>

                <x-modules.card title="Quick Actions">
                    <div class="space-y-3">
                        <a href="{{ route('admin.profile') }}">
                            <x-modules.button variant="outline" fullWidth>📝 Update Profile</x-modules.button>
                        </a>
                        <a href="{{ route('admin.change-password') }}">
                            <x-modules.button variant="outline" fullWidth>🔐 Change Password</x-modules.button>
                        </a>
                        <a href="{{ route('admin.create-admin') }}">
                            <x-modules.button variant="outline" fullWidth>👤 Create New Admin</x-modules.button>
                        </a>
                    </div>
                </x-modules.card>
            </div>
        </div>
    </div>
</x-layout>
