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

    <div class="max-w-2xl mx-auto py-8 px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-100 rounded-full mb-4">
                <svg class="w-10 h-10 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Profile Settings</h1>
            <p class="text-gray-600">Manage your account and branch information</p>
        </div>

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

        <form action="{{ route('admin.update-profile') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Personal Information -->
            <x-modules.card class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </h2>

                <div class="space-y-4">
                    <x-modules.input 
                        type="text" 
                        name="username" 
                        label="Username" 
                        value="{{ old('username', Auth::guard('admin')->user()->username) }}" 
                        required 
                    />
                    
                    <x-modules.input 
                        type="text" 
                        name="branch_name" 
                        label="Branch Name" 
                        value="{{ old('branch_name', Auth::guard('admin')->user()->branch_name) }}" 
                        required 
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-modules.input 
                            type="text" 
                            name="fname" 
                            label="First Name" 
                            value="{{ old('fname', Auth::guard('admin')->user()->fname) }}" 
                            required 
                        />

                        <x-modules.input 
                            type="text" 
                            name="lname" 
                            label="Last Name" 
                            value="{{ old('lname', Auth::guard('admin')->user()->lname) }}" 
                            required 
                        />
                    </div>

                    <x-modules.input 
                        type="email" 
                        name="email" 
                        label="Email Address" 
                        value="{{ old('email', Auth::guard('admin')->user()->email) }}" 
                        required 
                    />

                    <x-modules.input 
                        type="tel" 
                        name="phone" 
                        label="Phone Number" 
                        value="{{ old('phone', Auth::guard('admin')->user()->phone) }}" 
                        required 
                    />

                    <x-modules.input 
                        type="text" 
                        name="address" 
                        label="Personal Address" 
                        value="{{ old('address', Auth::guard('admin')->user()->address) }}" 
                        required 
                    />
                </div>
            </x-modules.card>

            <!-- Branch Information -->
            <x-modules.card class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Branch Information
                </h2>
                <p class="text-sm text-gray-500 mb-4">This address is shown to customers and used for location services.</p>

                @if($otherBranches->count() > 0)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Other Branch Locations</label>
                        <select id="other-branches-select" class="form-select w-full" onchange="viewBranchAddress(this.value)">
                            <option value="">View other branches...</option>
                            @foreach($otherBranches as $branch)
                                <option value="{{ $branch->branch_address }}">
                                    {{ $branch->branch_name }} - {{ Str::limit($branch->branch_address, 40) }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-2" id="selected-branch-address"></p>
                    </div>
                @endif

                <x-modules.input 
                    type="text" 
                    name="branch_address" 
                    label="Your Branch Address" 
                    id="branch-address-input"
                    value="{{ old('branch_address', Auth::guard('admin')->user()->branch_address) }}" 
                    placeholder="Enter your branch/shop address"
                />
            </x-modules.card>

            <!-- Submit Button -->
            <x-modules.button type="submit" variant="primary" fullWidth size="lg">
                Save Changes
            </x-modules.button>
        </form>
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
