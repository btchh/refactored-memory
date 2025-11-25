<x-layout>
    <x-slot:title>User Profile</x-slot:title>
    <x-nav type="user" />

    <!-- ✅ Add extra top padding to avoid nav overlap -->
    <div class="min-h-screen bg-gray-100 pt-[7rem] pb-12">

        <main class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <x-modules.card class="p-6 md:p-8">
                    <!-- Alerts -->
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
        </main>
    </div>
</x-layout>
