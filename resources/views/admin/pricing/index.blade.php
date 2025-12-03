<x-layout>
    <x-slot name="title">Pricing Management</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <x-modules.page-header
            title="Pricing Management"
            subtitle="Manage your branch's services and products"
            icon="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
            gradient="teal"
        >
            <x-slot name="stats">
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-white/70 text-xs">Services</p>
                    <p class="text-xl font-bold">{{ $services->count() }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur rounded-xl px-4 py-2">
                    <p class="text-white/70 text-xs">Products</p>
                    <p class="text-xl font-bold">{{ $products->count() }}</p>
                </div>
            </x-slot>
        </x-modules.page-header>

        <!-- Tabs -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="border-b border-gray-200 px-6 bg-gray-50">
                <nav class="flex gap-1">
                    <button type="button" data-tab="services" class="pricing-tab active px-6 py-4 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 bg-white -mb-px rounded-t-lg">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Services
                    </button>
                    <button type="button" data-tab="products" class="pricing-tab px-6 py-4 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-t-lg transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Products
                    </button>
                </nav>
            </div>

            <!-- Services Panel -->
            <div id="services-panel" class="pricing-panel p-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Services</h2>
                        <p class="text-sm text-gray-500">Labor and actions performed on items</p>
                    </div>
                    <button type="button" onclick="openServiceModal()" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Service
                    </button>
                </div>

                @if($services->count() > 0)
                    @foreach(['clothes' => ['Clothes', 'bg-blue-500', 'from-blue-50'], 'comforter' => ['Comforter', 'bg-green-500', 'from-green-50'], 'shoes' => ['Shoes', 'bg-orange-500', 'from-orange-50']] as $type => $config)
                        @php $typeServices = $services->where('item_type', $type); @endphp
                        @if($typeServices->count() > 0)
                            <div class="mb-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 {{ $config[1] }} rounded-lg flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ $typeServices->count() }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">{{ $config[0] }}</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($typeServices as $service)
                                        <div class="group bg-gradient-to-br {{ $config[2] }} to-white rounded-xl p-5 border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-200">
                                            <div class="flex justify-between items-start mb-3">
                                                <div class="flex-1">
                                                    <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $service->service_name }}</h4>
                                                    @if($service->is_bundle)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-700 mt-1">Bundle</span>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-2xl font-bold text-green-600">₱{{ number_format($service->price, 0) }}</span>
                                                </div>
                                            </div>
                                            @if($service->description)
                                                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $service->description }}</p>
                                            @endif
                                            <div class="flex gap-2 pt-3 border-t border-gray-200/50">
                                                <button type="button" onclick='editService(@json($service))' class="flex-1 btn btn-sm bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-blue-300 hover:text-blue-600">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                                <button type="button" onclick="deleteService({{ $service->id }}, '{{ addslashes($service->service_name) }}')" class="btn btn-sm bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No services yet</h3>
                        <p class="text-gray-500 mb-6">Start by adding your first service</p>
                        <button type="button" onclick="openServiceModal()" class="btn btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Your First Service
                        </button>
                    </div>
                @endif
            </div>

            <!-- Products Panel -->
            <div id="products-panel" class="pricing-panel p-6 hidden">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Products</h2>
                        <p class="text-sm text-gray-500">Physical items and add-ons</p>
                    </div>
                    <button type="button" onclick="openProductModal()" class="btn btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </button>
                </div>

                @if($products->count() > 0)
                    @foreach(['clothes' => ['Clothes', 'bg-blue-500', 'from-blue-50'], 'comforter' => ['Comforter', 'bg-green-500', 'from-green-50'], 'shoes' => ['Shoes', 'bg-orange-500', 'from-orange-50']] as $type => $config)
                        @php $typeProducts = $products->where('item_type', $type); @endphp
                        @if($typeProducts->count() > 0)
                            <div class="mb-8">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 {{ $config[1] }} rounded-lg flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ $typeProducts->count() }}</span>
                                    </div>
                                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">{{ $config[0] }}</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($typeProducts as $product)
                                        <div class="group bg-gradient-to-br {{ $config[2] }} to-white rounded-xl p-5 border border-gray-200 hover:border-purple-300 hover:shadow-lg transition-all duration-200">
                                            <div class="flex justify-between items-start mb-3">
                                                <h4 class="font-bold text-gray-900 group-hover:text-purple-600 transition-colors">{{ $product->product_name }}</h4>
                                                <span class="text-2xl font-bold text-green-600">₱{{ number_format($product->price, 0) }}</span>
                                            </div>
                                            @if($product->description)
                                                <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $product->description }}</p>
                                            @endif
                                            <div class="flex gap-2 pt-3 border-t border-gray-200/50">
                                                <button type="button" onclick='editProduct(@json($product))' class="flex-1 btn btn-sm bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-purple-300 hover:text-purple-600">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit
                                                </button>
                                                <button type="button" onclick="deleteProduct({{ $product->id }}, '{{ addslashes($product->product_name) }}')" class="btn btn-sm bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-300">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">No products yet</h3>
                        <p class="text-gray-500 mb-6">Start by adding your first product</p>
                        <button type="button" onclick="openProductModal()" class="btn btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Your First Product
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Service Modal -->
    <div id="service-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeServiceModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="service-modal-title">Add Service</h3>
                    <button type="button" onclick="closeServiceModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="service-form">
                    <input type="hidden" id="service-id">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Service Name</label>
                            <input type="text" id="service-name" class="form-input rounded-xl" placeholder="e.g., Wash, Dry, Fold" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Item Type</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="service_item_type" value="clothes" class="hidden peer" required>
                                    <div class="p-3 text-center border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition-all">
                                        <span class="text-sm font-medium">Clothes</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="service_item_type" value="comforter" class="hidden peer">
                                    <div class="p-3 text-center border-2 border-gray-200 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition-all">
                                        <span class="text-sm font-medium">Comforter</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="service_item_type" value="shoes" class="hidden peer">
                                    <div class="p-3 text-center border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300 transition-all">
                                        <span class="text-sm font-medium">Shoes</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price (₱)</label>
                            <input type="number" id="service-price" class="form-input rounded-xl" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea id="service-description" class="form-textarea rounded-xl" rows="2" placeholder="Brief description of the service..."></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8">
                        <button type="button" class="flex-1 btn btn-outline rounded-xl" onclick="closeServiceModal()">Cancel</button>
                        <button type="submit" class="flex-1 btn btn-primary rounded-xl">Save Service</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="product-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeProductModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900" id="product-modal-title">Add Product</h3>
                    <button type="button" onclick="closeProductModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="product-form">
                    <input type="hidden" id="product-id">
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name</label>
                            <input type="text" id="product-name" class="form-input rounded-xl" placeholder="e.g., Detergent, Fabric Conditioner" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Item Type</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="product_item_type" value="clothes" class="hidden peer" required>
                                    <div class="p-3 text-center border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:border-blue-300 transition-all">
                                        <span class="text-sm font-medium">Clothes</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="product_item_type" value="comforter" class="hidden peer">
                                    <div class="p-3 text-center border-2 border-gray-200 rounded-xl peer-checked:border-green-500 peer-checked:bg-green-50 hover:border-green-300 transition-all">
                                        <span class="text-sm font-medium">Comforter</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="product_item_type" value="shoes" class="hidden peer">
                                    <div class="p-3 text-center border-2 border-gray-200 rounded-xl peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:border-orange-300 transition-all">
                                        <span class="text-sm font-medium">Shoes</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Price (₱)</label>
                            <input type="number" id="product-price" class="form-input rounded-xl" step="0.01" min="0" placeholder="0.00" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea id="product-description" class="form-textarea rounded-xl" rows="2" placeholder="Brief description of the product..."></textarea>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-8">
                        <button type="button" class="flex-1 btn btn-outline rounded-xl" onclick="closeProductModal()">Cancel</button>
                        <button type="submit" class="flex-1 btn btn-primary rounded-xl">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.pricingRoutes = {
            services: {
                store: '{{ route('admin.pricing.services.store') }}',
                update: '{{ route('admin.pricing.services.update', ['id' => '__ID__']) }}',
                delete: '{{ route('admin.pricing.services.delete', ['id' => '__ID__']) }}'
            },
            products: {
                store: '{{ route('admin.pricing.products.store') }}',
                update: '{{ route('admin.pricing.products.update', ['id' => '__ID__']) }}',
                delete: '{{ route('admin.pricing.products.delete', ['id' => '__ID__']) }}'
            },
            csrf: '{{ csrf_token() }}'
        };
    </script>
    @vite(['resources/js/pages/admin-pricing.js'])
    @endpush
</x-layout>
