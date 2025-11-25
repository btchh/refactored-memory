<x-layout>
    <x-slot:title>Create Admin</x-slot:title>

    <x-nav type="admin" />
    
    <!-- Data attributes for JavaScript -->
    <div data-send-otp-url="{{ route('admin.send-admin-otp') }}"
         data-verify-otp-url="{{ route('admin.verify-admin-otp') }}"
         style="display: none;"></div>

    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Create New Admin</h1>
                    <p class="text-gray-600 mt-2">Add a new administrator to the system</p>
                </div>

                <!-- Progress Steps -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 text-center">
                            <div id="step1-indicator" class="w-10 h-10 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">1</div>
                            <p class="text-sm mt-2 font-semibold text-blue-600">Contact Info</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-1"></div>
                        <div class="flex-1 text-center">
                            <div id="step2-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">2</div>
                            <p class="text-sm mt-2 text-gray-500">Verify OTP</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-300" id="progress-line-2"></div>
                        <div class="flex-1 text-center">
                            <div id="step3-indicator" class="w-10 h-10 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">3</div>
                            <p class="text-sm mt-2 text-gray-500">Admin Details</p>
                        </div>
                    </div>
                </div>

                <x-modules.card>
                    <!-- Step 1: Contact Information -->
                    <div id="step1" class="step-content">
                        <h2 class="text-xl font-bold mb-4">Step 1: Contact Information</h2>
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

                            <x-modules.button type="button" id="send-otp-btn" onclick="sendOTP()" variant="primary" fullWidth>
                                Send OTP
                            </x-modules.button>
                        </form>
                    </div>

                    <!-- Step 2: OTP Verification -->
                    <div id="step2" class="step-content hidden">
                        <h2 class="text-xl font-bold mb-4">Step 2: Verify OTP</h2>
                        <p class="text-gray-600 mb-4">Enter the OTP sent to your phone and email</p>
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
                                <x-modules.button type="button" id="verify-otp-btn" onclick="verifyOTP()" variant="primary" class="flex-1">
                                    Verify OTP
                                </x-modules.button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Admin Details -->
                    <div id="step3" class="step-content hidden">
                        <h2 class="text-xl font-bold mb-4">Step 3: Admin Details</h2>
                        <form action="{{ route('admin.create-admin') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" id="verified_email" name="email">
                            <input type="hidden" id="verified_phone" name="phone">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-modules.input 
                                    type="text" 
                                    name="admin_name" 
                                    label="Admin Username" 
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
                                    label="Address" 
                                    placeholder="Full address"
                                    required 
                                />

                                <div>
                                    <x-modules.input 
                                        type="password" 
                                        id="password"
                                        name="password" 
                                        label="Password" 
                                        placeholder="Min 8 characters"
                                        required 
                                    />
                                    <!-- Password Strength Indicator -->
                                    <div class="mt-2">
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div id="password-strength-bar" class="h-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                        <p id="password-strength-text" class="text-xs mt-1 text-gray-500"></p>
                                    </div>
                                </div>

                                <div>
                                    <x-modules.input 
                                        type="password" 
                                        id="password_confirmation"
                                        name="password_confirmation" 
                                        label="Confirm Password" 
                                        placeholder="Confirm password"
                                        required 
                                    />
                                    <!-- Password Match Indicator -->
                                    <p id="password-match-text" class="text-xs mt-2"></p>
                                </div>
                            </div>

                            <div class="flex gap-3 pt-4">
                                <x-modules.button type="button" onclick="goToStep(2)" variant="secondary" class="flex-1">
                                    Back
                                </x-modules.button>
                                <x-modules.button type="submit" variant="success" class="flex-1">
                                    Create Admin
                                </x-modules.button>
                            </div>
                        </form>
                    </div>
                </x-modules.card>
            </div>
        </div>
    </div>

</x-layout>
