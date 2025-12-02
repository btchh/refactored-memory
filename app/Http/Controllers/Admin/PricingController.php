<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PricingController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }
    /**
     * Display pricing management page
     */
    public function index()
    {
        $adminId = Auth::guard('admin')->id();
        
        $services = Service::forAdmin($adminId)->orderBy('item_type')->orderBy('service_name')->get();
        $products = Product::forAdmin($adminId)->orderBy('item_type')->orderBy('product_name')->get();

        return view('admin.pricing.index', compact('services', 'products'));
    }

    /**
     * Store a new service
     */
    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $validated['admin_id'] = Auth::guard('admin')->id();
            $service = Service::create($validated);

            $this->auditService->logCreate(Service::class, $service, "Created service: {$service->service_name} (₱{$service->price})");

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'service' => $service,
            ]);
        } catch (\Exception $e) {
            Log::error('Service creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing service
     */
    public function updateService(Request $request, $id)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $adminId = Auth::guard('admin')->id();
            $service = Service::forAdmin($adminId)->findOrFail($id);
            $oldValues = $service->toArray();
            $service->update($validated);

            $this->auditService->logUpdate(Service::class, $service, $oldValues, "Updated service: {$service->service_name}");

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'service' => $service,
            ]);
        } catch (\Exception $e) {
            Log::error('Service update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a service
     */
    public function deleteService($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            $service = Service::forAdmin($adminId)->findOrFail($id);
            
            // Check if service is used in any transactions
            $usageCount = DB::table('service_transactions')->where('service_id', $id)->count();
            
            if ($usageCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete service. It is used in {$usageCount} booking(s).",
                ], 400);
            }

            $serviceName = $service->service_name;
            $this->auditService->logDelete(Service::class, $service, "Deleted service: {$serviceName}");
            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Service deletion failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new product
     */
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $validated['admin_id'] = Auth::guard('admin')->id();
            $product = Product::create($validated);

            $this->auditService->logCreate(Product::class, $product, "Created product: {$product->product_name} (₱{$product->price})");

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            Log::error('Product creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'item_type' => 'required|in:clothes,comforter,shoes',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        try {
            $adminId = Auth::guard('admin')->id();
            $product = Product::forAdmin($adminId)->findOrFail($id);
            $oldValues = $product->toArray();
            $product->update($validated);

            $this->auditService->logUpdate(Product::class, $product, $oldValues, "Updated product: {$product->product_name}");

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'product' => $product,
            ]);
        } catch (\Exception $e) {
            Log::error('Product update failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a product
     */
    public function deleteProduct($id)
    {
        try {
            $adminId = Auth::guard('admin')->id();
            $product = Product::forAdmin($adminId)->findOrFail($id);
            
            // Check if product is used in any transactions
            $usageCount = DB::table('product_transactions')->where('product_id', $id)->count();
            
            if ($usageCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete product. It is used in {$usageCount} booking(s).",
                ], 400);
            }

            $productName = $product->product_name;
            $this->auditService->logDelete(Product::class, $product, "Deleted product: {$productName}");
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Product deletion failed', ['error' => $e->getMessage(), 'id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage(),
            ], 500);
        }
    }
}
