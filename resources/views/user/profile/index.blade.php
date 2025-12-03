<x-layout>
    <x-slot:title>My Profile</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Page Header -->
        <x-modules.page-header
            title="My Profile"
            subtitle="Manage your account information"
            icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            gradient="violet"
        />

        <!-- Success Message -->
        @if(session('success'))
            <x-modules.alert type="success" dismissible class="mb-6">
                {{ session('success') }}
            </x-modules.alert>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <x-modules.alert type="error" dismissible class="mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-modules.alert>
        @endif

        <form action="{{ route('user.update-profile') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Account Information -->
            <x-modules.card class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Account Information
                </h2>

                <div class="space-y-4">
                    <x-modules.input 
                        type="text" 
                        name="username" 
                        label="Username"
                        value="{{ old('username', Auth::user()->username) }}" 
                        required 
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-modules.input 
                            type="text" 
                            name="fname" 
                            label="First Name"
                            value="{{ old('fname', Auth::user()->fname) }}" 
                            required 
                        />

                        <x-modules.input 
                            type="text" 
                            name="lname" 
                            label="Last Name"
                            value="{{ old('lname', Auth::user()->lname) }}" 
                            required 
                        />
                    </div>

                    <x-modules.input 
                        type="email" 
                        name="email" 
                        label="Email Address"
                        value="{{ old('email', Auth::user()->email) }}" 
                        required 
                    />

                    <x-modules.input 
                        type="tel" 
                        name="phone" 
                        label="Phone Number"
                        value="{{ old('phone', Auth::user()->phone) }}" 
                        required 
                    />

                    <x-modules.input 
                        type="text" 
                        name="address" 
                        label="Address"
                        value="{{ old('address', Auth::user()->address) }}" 
                        placeholder="Enter your address"
                        required 
                    />
                </div>
            </x-modules.card>

            <!-- Submit Button -->
            <x-modules.button type="submit" variant="primary" fullWidth size="lg">
                Save Changes
            </x-modules.button>
        </form>
    </div>
</x-layout>
