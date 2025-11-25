<x-layout>
    <x-slot:title>Admin Profile</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-2xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-500 to-teal-500 rounded-full mb-4">
                    <span class="text-4xl">👨‍💼</span>
                </div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent mb-2">
                    Admin Profile
                </h1>
                <p class="text-gray-600">Manage your admin account information</p>
            </div>

            <x-modules.card title="Profile Information" class="shadow-xl border-2 border-gray-100">
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
                    <x-modules.button type="submit" variant="primary" class="flex-1 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 transition-all duration-300 hover:scale-105">
                        💾 Update Profile
                    </x-modules.button>
                    <a href="{{ route('admin.dashboard') }}" class="flex-1">
                        <x-modules.button type="button" variant="secondary" fullWidth class="hover:scale-105 transition-transform">
                            ❌ Cancel
                        </x-modules.button>
                    </a>
                </div>
            </form>
        </x-modules.card>
        </div>
    </div>
</x-layout>
