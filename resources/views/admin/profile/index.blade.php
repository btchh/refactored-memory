<x-layout>
    <x-slot:title>Admin Profile</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-2xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-50 rounded-full mb-4">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Admin Profile
                </h1>
                <p class="text-gray-600">Manage your admin account information</p>
            </div>

            <x-modules.card class="p-6 md:p-8">
                <form action="{{ route('admin.update-profile') }}" method="POST" class="space-y-6">
                    @csrf

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
                        label="Personal Address" 
                        value="{{ Auth::guard('admin')->user()->address }}" 
                        required 
                    />

                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Branch Information</h3>
                        <p class="text-sm text-gray-600 mb-4">This information will be used for location tracking and routing.</p>
                        
                        <x-modules.input 
                            type="text" 
                            name="branch_address" 
                            label="Branch Address" 
                            value="{{ Auth::guard('admin')->user()->branch_address }}" 
                            placeholder="Enter your branch/shop address"
                        />
                    </div>

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
