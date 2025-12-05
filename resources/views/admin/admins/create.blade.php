<x-layout>
    <x-slot:title>Create Admin</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Hero Header -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-xl mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h1 class="text-5xl font-black text-white mb-3">Create New Admin</h1>
                <p class="text-xl text-white/80">Add a new administrator to the system</p>
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 text-center">
                    <div id="step1-indicator" class="w-12 h-12 mx-auto rounded-full bg-wash text-white flex items-center justify-center font-bold text-lg">1</div>
                    <p class="text-sm mt-2 font-bold text-wash">Contact Info</p>
                </div>
                <div class="flex-1 h-2 bg-gray-300 rounded-full mx-2" id="progress-line-1"></div>
                <div class="flex-1 text-center">
                    <div id="step2-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg">2</div>
                    <p class="text-sm mt-2 text-gray-500 font-bold">Verify OTP</p>
                </div>
                <div class="flex-1 h-2 bg-gray-300 rounded-full mx-2" id="progress-line-2"></div>
                <div class="flex-1 text-center">
                    <div id="step3-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg">3</div>
                    <p class="text-sm mt-2 text-gray-500 font-bold">Admin Details</p>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6 md:p-8">
            <!-- Step 1: Contact Information -->
            <div id="step1" class="step-content">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-wash/10 rounded-xl mb-3">
                        <svg class="w-8 h-8 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Contact Information</h2>
                    <p class="text-gray-600">Enter email and phone to receive OTP</p>
                </div>
                <form id="contact-form" class="space-y-4">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span class="text-error">*</span></label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="admin@example.com"
                            required 
                        />
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number <span class="text-error">*</span></label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-input" 
                            placeholder="09123456789"
                            required 
                        />
                    </div>

                    <div id="contact-error" class="hidden text-error text-sm"></div>

                    <button type="button" onclick="sendOTP()" class="btn btn-primary btn-lg w-full">
                        Send OTP
                    </button>
                </form>
            </div>

            <!-- Step 2: OTP Verification -->
            <div id="step2" class="step-content hidden">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-wash/10 rounded-xl mb-3">
                        <svg class="w-8 h-8 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Verify OTP</h2>
                    <p class="text-gray-600">Enter the 6-digit code sent to your phone and email</p>
                </div>
                <form id="otp-form" class="space-y-4">
                    <div class="form-group">
                        <label for="otp" class="form-label">OTP Code <span class="text-error">*</span></label>
                        <input 
                            type="text" 
                            id="otp" 
                            name="otp" 
                            class="form-input" 
                            placeholder="Enter 6-digit code"
                            maxlength="6"
                            required 
                        />
                    </div>

                    <div id="otp-error" class="hidden text-error text-sm"></div>

                    <div class="flex gap-3">
                        <button type="button" onclick="goToStep(1)" class="btn btn-secondary flex-1">
                            Back
                        </button>
                        <button type="button" onclick="verifyOTP()" class="btn btn-primary flex-1">
                            Verify OTP
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Admin Details -->
            <div id="step3" class="step-content hidden">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-wash/10 rounded-xl mb-3">
                        <svg class="w-8 h-8 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 mb-2">Admin Details</h2>
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
                        <div class="form-group">
                            <label for="username" class="form-label">Username <span class="text-error">*</span></label>
                            <input 
                                type="text" 
                                name="username" 
                                id="username"
                                class="form-input" 
                                placeholder="Enter username"
                                required 
                            />
                        </div>

                        <div class="form-group">
                            <label for="fname" class="form-label">First Name <span class="text-error">*</span></label>
                            <input 
                                type="text" 
                                name="fname" 
                                id="fname"
                                class="form-input" 
                                placeholder="First name"
                                required 
                            />
                        </div>

                        <div class="form-group">
                            <label for="lname" class="form-label">Last Name <span class="text-error">*</span></label>
                            <input 
                                type="text" 
                                name="lname" 
                                id="lname"
                                class="form-input" 
                                placeholder="Last name"
                                required 
                            />
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Personal Address <span class="text-error">*</span></label>
                            <input 
                                type="text" 
                                name="address" 
                                id="address"
                                class="form-input" 
                                placeholder="Full address"
                                required 
                            />
                        </div>

                        <div class="form-group">
                            <label for="branch_name" class="form-label">Branch Name <span class="text-error">*</span></label>
                            <input 
                                type="text" 
                                name="branch_name" 
                                id="branch_name"
                                class="form-input" 
                                placeholder="e.g., WashHour Main"
                                required 
                            />
                        </div>

                        <div class="form-group">
                            <label for="branch_address" class="form-label">Branch Address</label>
                            <input 
                                type="text" 
                                name="branch_address" 
                                id="branch_address"
                                class="form-input" 
                                placeholder="Branch location"
                            />
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password <span class="text-error">*</span></label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password"
                                class="form-input" 
                                placeholder="Min 8 characters"
                                required 
                            />
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-error">*</span></label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation"
                                class="form-input" 
                                placeholder="Confirm password"
                                required 
                            />
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="goToStep(2)" class="btn btn-secondary flex-1">
                            Back
                        </button>
                        <button type="submit" class="btn btn-primary flex-1">
                            Create Admin
                        </button>
                    </div>
                </form>
            </div>
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
