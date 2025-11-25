<x-layout>
    <x-slot:title>Create Admin</x-slot:title>

    <div class="flex items-center justify-center min-h-full py-8">
        <div class="w-full max-w-3xl px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full mb-4">
                    <span class="text-4xl">👥</span>
                </div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mb-2">
                    Create New Admin
                </h1>
                <p class="text-gray-600">Add a new administrator to the system</p>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8 bg-white rounded-2xl shadow-lg p-6 border-2 border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex-1 text-center">
                        <div id="step1-indicator" class="w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white flex items-center justify-center font-bold text-lg shadow-lg">1</div>
                        <p class="text-sm mt-2 font-semibold text-purple-600">📧 Contact Info</p>
                    </div>
                    <div class="flex-1 h-2 bg-gray-300 rounded-full mx-2" id="progress-line-1"></div>
                    <div class="flex-1 text-center">
                        <div id="step2-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg">2</div>
                        <p class="text-sm mt-2 text-gray-500">🔐 Verify OTP</p>
                    </div>
                    <div class="flex-1 h-2 bg-gray-300 rounded-full mx-2" id="progress-line-2"></div>
                    <div class="flex-1 text-center">
                        <div id="step3-indicator" class="w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg">3</div>
                        <p class="text-sm mt-2 text-gray-500">✍️ Admin Details</p>
                    </div>
                </div>
            </div>

            <x-modules.card class="shadow-xl border-2 border-gray-100">
                <!-- Step 1: Contact Information -->
                <div id="step1" class="step-content">
                    <div class="text-center mb-6">
                        <span class="text-5xl mb-3 block">📧</span>
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

                            <x-modules.button type="button" onclick="sendOTP()" variant="primary" fullWidth class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all duration-300 hover:scale-105">
                                📤 Send OTP
                            </x-modules.button>
                        </form>
                    </div>

                    <!-- Step 2: OTP Verification -->
                    <div id="step2" class="step-content hidden">
                        <div class="text-center mb-6">
                            <span class="text-5xl mb-3 block">🔐</span>
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
                                <x-modules.button type="button" onclick="goToStep(1)" variant="secondary" class="flex-1 hover:scale-105 transition-transform">
                                    ⬅️ Back
                                </x-modules.button>
                                <x-modules.button type="button" onclick="verifyOTP()" variant="primary" class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all duration-300 hover:scale-105">
                                    ✅ Verify OTP
                                </x-modules.button>
                            </div>
                        </form>
                    </div>

                    <!-- Step 3: Admin Details -->
                    <div id="step3" class="step-content hidden">
                        <div class="text-center mb-6">
                            <span class="text-5xl mb-3 block">✍️</span>
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Admin Details</h2>
                            <p class="text-gray-600">Complete the admin account information</p>
                        </div>
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
                                <x-modules.button type="button" onclick="goToStep(2)" variant="secondary" class="flex-1 hover:scale-105 transition-transform">
                                    ⬅️ Back
                                </x-modules.button>
                                <x-modules.button type="submit" variant="success" class="flex-1 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 transition-all duration-300 hover:scale-105">
                                    ✨ Create Admin
                                </x-modules.button>
                            </div>
                        </form>
                    </div>
                </x-modules.card>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let verifiedEmail = '';
        let verifiedPhone = '';

        function goToStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
            
            // Show target step
            document.getElementById('step' + step).classList.remove('hidden');
            
            // Update progress indicators
            for (let i = 1; i <= 3; i++) {
                const indicator = document.getElementById('step' + i + '-indicator');
                const line = document.getElementById('progress-line-' + i);
                
                if (i < step) {
                    indicator.className = 'w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-green-500 to-teal-500 text-white flex items-center justify-center font-bold text-lg shadow-lg';
                    if (line) line.className = 'flex-1 h-2 bg-gradient-to-r from-green-500 to-teal-500 rounded-full mx-2';
                } else if (i === step) {
                    indicator.className = 'w-12 h-12 mx-auto rounded-full bg-gradient-to-br from-purple-600 to-pink-600 text-white flex items-center justify-center font-bold text-lg shadow-lg';
                    if (line) line.className = 'flex-1 h-2 bg-gray-300 rounded-full mx-2';
                } else {
                    indicator.className = 'w-12 h-12 mx-auto rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg';
                    if (line) line.className = 'flex-1 h-2 bg-gray-300 rounded-full mx-2';
                }
            }
            
            currentStep = step;
        }

        function sendOTP() {
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const errorDiv = document.getElementById('contact-error');
            const button = event.target;

            if (!email || !phone) {
                errorDiv.textContent = 'Please fill in all fields';
                errorDiv.classList.remove('hidden');
                return;
            }

            // Disable button to prevent double clicks
            button.disabled = true;
            button.textContent = 'Sending...';
            errorDiv.classList.add('hidden');

            console.log('Sending OTP to:', { email, phone });

            fetch('{{ route('admin.send-admin-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email, phone })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json().then(data => ({ status: response.status, data }));
            })
            .then(({ status, data }) => {
                console.log('Response status:', status);
                console.log('Response data:', data);
                console.log('Data.success:', data.success);
                
                button.disabled = false;
                button.textContent = 'Send OTP';
                
                // Check if successful (status 200 or data.success is true)
                if (data.success === true || (status === 200 && data.success !== false)) {
                    console.log('OTP sent successfully, moving to step 2');
                    verifiedEmail = email;
                    verifiedPhone = phone;
                    goToStep(2);
                } else {
                    console.log('OTP send failed:', data.message);
                    errorDiv.textContent = data.message || 'Failed to send OTP';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.textContent = 'Send OTP';
                errorDiv.textContent = 'An error occurred: ' + error.message;
                errorDiv.classList.remove('hidden');
            });
        }

        function verifyOTP() {
            const otp = document.getElementById('otp').value;
            const errorDiv = document.getElementById('otp-error');

            if (!otp || otp.length !== 6) {
                errorDiv.textContent = 'Please enter a valid 6-digit OTP';
                errorDiv.classList.remove('hidden');
                return;
            }

            errorDiv.classList.add('hidden');

            fetch('{{ route('admin.verify-admin-otp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ phone: verifiedPhone, otp })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('verified_email').value = verifiedEmail;
                    document.getElementById('verified_phone').value = verifiedPhone;
                    goToStep(3);
                } else {
                    errorDiv.textContent = data.message || 'Invalid OTP';
                    errorDiv.classList.remove('hidden');
                }
            })
            .catch(error => {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.classList.remove('hidden');
            });
        }
    </script>
</x-layout>
