<x-layout>
    <x-slot:title>User Profile</x-slot:title>

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
                    Update Profile
                </h1>
                <p class="text-gray-600">Manage your account information</p>
            </div>

            <x-modules.card class="p-6 md:p-8">
                    <!-- Alerts -->
                    @if (isset($errors) && $errors->any())
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

                    <!-- Profile Form -->
                    <form action="{{ route('user.update-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- Username -->
                        <x-modules.input type="text" name="username" label="Username"
                            value="{{ Auth::user()->username }}" required />

                        <!-- First Name -->
                        <x-modules.input type="text" name="fname" label="First Name"
                            value="{{ Auth::user()->fname }}" required />

                        <!-- Last Name -->
                        <x-modules.input type="text" name="lname" label="Last Name"
                            value="{{ Auth::user()->lname }}" required />

                        <!-- Email -->
                        <x-modules.input type="email" name="email" label="Email"
                            value="{{ Auth::user()->email }}" required />

                        <!-- Phone -->
                        <x-modules.input type="text" name="phone" label="Phone Number"
                            value="{{ Auth::user()->phone }}" required />

                        <!-- Address -->
                        <x-modules.input type="text" name="address" label="Address"
                            value="{{ Auth::user()->address }}" required />

                        <!-- Submit Button -->
                        <x-modules.button type="submit" variant="primary" fullWidth size="md">
                            Update Profile
                        </x-modules.button>
                    </form>
                </x-modules.card>
        </div>
    </div>
</x-layout>
