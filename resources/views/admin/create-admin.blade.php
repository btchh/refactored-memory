<x-layout>
    <x-slot:title>Create Admin</x-slot:title>

    <x-nav type="admin" />

    <div class="container mx-auto px-4 py-8">
        <div class="page-header mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Create New Admin</h1>
        <p class="text-gray-600 mt-2">Add a new administrator to the system</p>
    </div>

    <div class="max-w-3xl">
        <x-modules.card title="Admin Details">
            <form action="{{ route('admin.store-admin') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-modules.input 
                        type="text" 
                        name="admin_name" 
                        label="Admin Name" 
                        value="{{ old('admin_name') }}"
                        placeholder="Enter admin username"
                        required 
                    />

                    <x-modules.input 
                        type="email" 
                        name="email" 
                        label="Email Address" 
                        value="{{ old('email') }}"
                        placeholder="admin@example.com"
                        required 
                    />

                    <x-modules.input 
                        type="text" 
                        name="fname" 
                        label="First Name" 
                        value="{{ old('fname') }}"
                        placeholder="First name"
                        required 
                    />

                    <x-modules.input 
                        type="text" 
                        name="lname" 
                        label="Last Name" 
                        value="{{ old('lname') }}"
                        placeholder="Last name"
                        required 
                    />

                    <x-modules.input 
                        type="tel" 
                        name="phone" 
                        label="Phone Number" 
                        value="{{ old('phone') }}"
                        placeholder="+1234567890"
                        required 
                    />

                    <x-modules.input 
                        type="text" 
                        name="address" 
                        label="Address" 
                        value="{{ old('address') }}"
                        placeholder="Full address"
                        required 
                    />

                    <x-modules.input 
                        type="password" 
                        name="password" 
                        label="Password" 
                        placeholder="Min 8 characters"
                        required 
                    />

                    <x-modules.input 
                        type="password" 
                        name="password_confirmation" 
                        label="Confirm Password" 
                        placeholder="Confirm password"
                        required 
                    />
                </div>

                <div class="flex gap-3 pt-4">
                    <x-modules.button type="submit" variant="success" class="flex-1">
                        Create Admin
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
