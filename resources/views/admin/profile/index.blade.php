<x-layout>
    <x-slot:title>Admin Profile</x-slot:title>

    @php
        // Get all branch addresses from other admins
        $otherBranches = \App\Models\Admin::where('id', '!=', Auth::guard('admin')->id())
            ->whereNotNull('branch_address')
            ->where('branch_address', '!=', '')
            ->select('id', 'branch_name', 'branch_address')
            ->get();
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Hero Header with Admin Information -->
        <div class="relative bg-gradient-to-br from-wash via-wash-dark to-gray-900 rounded-2xl p-12 overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
            </div>
            
            <!-- Content -->
            <div class="relative">
                <div class="flex items-center gap-4 mb-3">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-5xl font-black text-white">{{ Auth::guard('admin')->user()->fname }} {{ Auth::guard('admin')->user()->lname }}</h1>
                        <p class="text-xl text-white/80 mt-1">{{ Auth::guard('admin')->user()->branch_name }}</p>
                    </div>
                </div>
                <p class="text-lg text-white/70">Manage your account and branch information</p>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert alert-error">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-bold mb-2">Please correct the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.update-profile') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Personal Information Card -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Information
                    </h2>
                </div>

                <div class="space-y-4">
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username" class="form-label">
                            Username <span class="text-error">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            class="form-input @error('username') error @enderror"
                            value="{{ old('username', Auth::guard('admin')->user()->username) }}" 
                            required 
                        />
                        @error('username')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Branch Name -->
                    <div class="form-group">
                        <label for="branch_name" class="form-label">
                            Branch Name <span class="text-error">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="branch_name"
                            name="branch_name" 
                            class="form-input @error('branch_name') error @enderror"
                            value="{{ old('branch_name', Auth::guard('admin')->user()->branch_name) }}" 
                            required 
                        />
                        @error('branch_name')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- First and Last Name -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="fname" class="form-label">
                                First Name <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="fname"
                                name="fname" 
                                class="form-input @error('fname') error @enderror"
                                value="{{ old('fname', Auth::guard('admin')->user()->fname) }}" 
                                required 
                            />
                            @error('fname')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="lname" class="form-label">
                                Last Name <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="lname"
                                name="lname" 
                                class="form-input @error('lname') error @enderror"
                                value="{{ old('lname', Auth::guard('admin')->user()->lname) }}" 
                                required 
                            />
                            @error('lname')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            Email Address <span class="text-error">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            name="email" 
                            class="form-input @error('email') error @enderror"
                            value="{{ old('email', Auth::guard('admin')->user()->email) }}" 
                            required 
                        />
                        @error('email')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone" class="form-label">
                            Phone Number <span class="text-error">*</span>
                        </label>
                        <input 
                            type="tel" 
                            id="phone"
                            name="phone" 
                            class="form-input @error('phone') error @enderror"
                            value="{{ old('phone', Auth::guard('admin')->user()->phone) }}" 
                            required 
                        />
                        @error('phone')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Personal Address -->
                    <div class="form-group">
                        <label for="address" class="form-label">
                            Personal Address <span class="text-error">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="address"
                            name="address" 
                            class="form-input @error('address') error @enderror"
                            value="{{ old('address', Auth::guard('admin')->user()->address) }}" 
                            required 
                        />
                        @error('address')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Branch Information Card -->
            <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Branch Information
                    </h2>
                </div>
                <p class="text-sm text-gray-600 mb-4">This address is shown to customers and used for location services.</p>

                @if($otherBranches->count() > 0)
                    <div class="mb-4 p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <label for="other-branches-select" class="form-label">Other Branch Locations</label>
                        <select id="other-branches-select" class="form-select" onchange="viewBranchAddress(this.value)">
                            <option value="">View other branches...</option>
                            @foreach($otherBranches as $branch)
                                <option value="{{ $branch->branch_address }}">
                                    {{ $branch->branch_name }} - {{ Str::limit($branch->branch_address, 40) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-600 mt-2" id="selected-branch-address"></p>
                    </div>
                @endif

                <div class="form-group">
                    <label for="branch-address-input" class="form-label">
                        Your Branch Address
                    </label>
                    <input 
                        type="text" 
                        id="branch-address-input"
                        name="branch_address" 
                        class="form-input @error('branch_address') error @enderror"
                        value="{{ old('branch_address', Auth::guard('admin')->user()->branch_address) }}" 
                        placeholder="Enter your branch/shop address"
                    />
                    @error('branch_address')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="btn btn-primary btn-lg flex-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg sm:w-auto">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </a>
            </div>
        </form>

        <!-- Additional Actions Card -->
        <div class="bg-white rounded-2xl border-2 border-gray-200 p-6">
            <h2 class="text-xl font-black text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-wash" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Account Settings
            </h2>
            <div class="space-y-3">
                <a href="{{ route('admin.change-password') }}" class="flex items-center justify-between p-4 rounded-xl border-2 border-gray-200 hover:border-wash hover:bg-wash/5 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-wash/10 rounded-xl flex items-center justify-center group-hover:bg-wash group-hover:scale-110 transition-all">
                            <svg class="w-5 h-5 text-wash group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">Change Password</p>
                            <p class="text-sm text-gray-600">Update your account password</p>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-wash transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function viewBranchAddress(address) {
            const display = document.getElementById('selected-branch-address');
            if (address) {
                display.textContent = 'Full address: ' + address;
                display.classList.remove('hidden');
            } else {
                display.textContent = '';
            }
        }
    </script>
    @endpush
</x-layout>
