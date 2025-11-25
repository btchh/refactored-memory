<x-layout>
    <x-slot:title>Admin Profile</x-slot:title>

    <x-nav type="admin" />

    <div class="container mx-auto px-4 py-8">
        <div class="page-header mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Update Profile</h1>
        <p class="text-gray-600 mt-2">Manage your admin account information</p>
    </div>

    <div class="max-w-2xl">
        <x-modules.card title="Profile Information">
            <form action="{{ route('admin.update-profile') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <x-modules.input 
                    type="text" 
                    name="admin_name" 
                    label="Admin Name" 
                    value="{{ Auth::guard('admin')->user()->admin_name }}" 
                    required 
                />

                <x-modules.input 
                    type="email" 
                    name="email" 
                    label="Email Address" 
                    value="{{ Auth::guard('admin')->user()->email }}" 
                    required 
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="text" 
                        name="fname" 
                        label="First Name" 
                        value="{{ Auth::guard('admin')->user()->fname }}" 
                        required 
                    />

                    <x-modules.input 
                        type="text" 
                        name="lname" 
                        label="Last Name" 
                        value="{{ Auth::guard('admin')->user()->lname }}" 
                        required 
                    />
                </div>

                <x-modules.input 
                    type="tel" 
                    name="phone" 
                    label="Phone Number" 
                    value="{{ Auth::guard('admin')->user()->phone }}" 
                    required 
                />

                <x-modules.input 
                    type="text" 
                    name="address" 
                    label="Address" 
                    value="{{ Auth::guard('admin')->user()->address }}" 
                    required 
                />

                <div class="flex gap-3 pt-4">
                    <x-modules.button type="submit" variant="primary" class="flex-1">
                        Update Profile
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
