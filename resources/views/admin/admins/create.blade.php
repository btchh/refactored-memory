<x-layout>
    <x-slot:title>Create Admin</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-3xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-50 rounded-full mb-4">
                    <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Create New Admin
                </h1>
                <p class="text-gray-600">Add a new administrator to the system</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8 bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 text-center">
                        <div id="step1-indicator" class="w-12 h-12 mx-auto rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-lg">1</div>
                        <p class="text-sm mt-2 font-semibold text-primary-600">Contact Info</p>
                    </div>
                    <div class="flex-1 h-2 bg-gray-300 rounded-full mx-2" id="progress-line-1"></div>
                    <div class="flex-1 text-center">
                        <div id="step2-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg">2</div>
                        <p class="text-sm mt-2 text-gray-500">Verify OTP</p>
                    </div>
                    <div class="flex-1 h-2 bg-gray-300 rounded-full mx-2" id="progress-line-2"></div>
                    <div class="flex-1 text-center">
                        <div id="step3-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg">3</div>
                        <p class="text-sm mt-2 text-gray-500">Admin Details</p>
                    </div>
                </div>
            </div>

            <x-modules.card class="p-6 md:p-8">
                <!-- Step 1: Contact Information -->
                <div id="step1" class="step-content">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 rounded-full mb-3">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Contact Information</h2>
                        <p class="text-gray-600">Enter email and phone to receive OTP</p>
                    </div>
                    <form id="contact-form" class="space-y-4">
                            <x-modules.input 
                                type="email" 
                                id="email" 
                                name="email" 
                                label="Email Address" 
                                placeholder="admin@example.com"
                                required 
                            />

                            <x-modules.input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                label="Phone Number" 
                                placeholder="09123456789"
                                required 
                            />

                            <div id="contact-error" class="hidden text-red-600 text-sm"></div>

                            <x-modules.button type="button" onclick="sendOTP()" variant="primary" fullWidth size="md">
                                Send OTP
                            </x-modules.button>
                        </form>
                    </div>

                    <!-- Step 2: OTP Verification -->
                    <div id="step2" class="step-content hidden">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 rounded-full mb-3">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Verify OTP</h2>
                            <p class="text-gray-600">Enter the 6-digit code sent to your phone and email</p>
                        </div>
                        <form id="otp-form" class="space-y-4">
                            <x-modules.input 
                                type="text" 
                                id="otp" 
                                name="otp" 
                                label="OTP Code" 
                                placeholder="Enter 6-digit code"
                                maxlength="6"
                                required 
                            />

                            <div id="otp-error" class="hidden text-red-600 text-sm"></div>

                            <div class="flex gap-3">
                                <x-modules.button type="button" onclick="goToStep(1)" variant="secondary" class="flex-1">
                                    Back
                                </x-modules.button>
                                <x-modules.button type="button" onclick="verifyOTP()" variant="primary" class="flex-1">
                                    Verify OTP
                                </x-modules.button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Admin Details -->
                    <div id="step3" class="step-content hidden">
                        <div class="text-center mb-6">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-50 rounded-full mb-3">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Admin Details</h2>
                            <p class="text-gray-600">Complete the admin account information</p>
                        </div>
                        <form action="{{ route('admin.create-admin') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" id="verified_email" name="email">
                            <input type="hidden" id="verified_phone" name="phone">

                            <!-- Alert Messages -->
                            @if (isset($errors) && $errors->any())
                                <x-modules.alert type="error" dismissible>
                                    <ul class="list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </x-modules.alert>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-modules.input 
                                    type="text" 
                                    name="username" 
                                    label="Username" 
                                    placeholder="Enter username"
                                    required 
                                />

                                <x-modules.input 
                                    type="text" 
                                    name="fname" 
                                    label="First Name" 
                                    placeholder="First name"
                                    required 
                                />

                                <x-modules.input 
                                    type="text" 
                                    name="lname" 
                                    label="Last Name" 
                                    placeholder="Last name"
                                    required 
                                />

                                <x-modules.input 
                                    type="text" 
                                    name="address" 
                                    label="Personal Address" 
                                    placeholder="Full address"
                                    required 
                                />

                                <x-modules.input 
                                    type="text" 
                                    name="branch_name" 
                                    label="Branch Name" 
                                    placeholder="e.g., WashHour Main"
                                    required 
                                />

                                <x-modules.input 
                                    type="text" 
                                    name="branch_address" 
                                    label="Branch Address" 
                                    placeholder="Branch location"
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
                                <x-modules.button type="button" onclick="goToStep(2)" variant="secondary" class="flex-1">
                                    Back
                                </x-modules.button>
                                <x-modules.button type="submit" variant="primary" class="flex-1">
                                    Create Admin
                                </x-modules.button>
                            </div>
                        </form>
                    </div>
                </x-modules.card>
        </div>
    </div>

    <script>
        // Pass routes to JavaScript
        window.routes = {
            sendAdminOtp: '{{ route('admin.send-admin-otp') }}',
            verifyAdminOtp: '{{ route('admin.verify-admin-otp') }}'
        };
    </script>
    @vite(['resources/js/pages/admin-creation.js'])
</x-layout>
