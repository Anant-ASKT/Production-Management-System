<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Category;
use App\Services\WooCommerceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminSupplierWebsiteController extends Controller
{
    /**
     * Display the Supplier Website & Store Management dashboard.
     */
    public function index($supplierId, WooCommerceService $wcService)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $hasCredentials = !empty($supplier->store_url) && 
                          !empty($supplier->consumer_key) && 
                          !empty($supplier->consumer_secret);

        $connection = [
            'checked' => false,
            'success' => false,
            'message' => 'Credentials not configured.',
            'total_products' => 0
        ];

        if ($hasCredentials) {
            $test = $wcService->testStoreConnection($supplier);
            $connection = array_merge(['checked' => true], $test);
        }

        // Local published products count for this supplier
        $localPublishedCount = DB::table('published_products')
            ->where('target_supplier_id', $supplier->sno)
            ->count();

        // Local categories count for this supplier
        $localCategoriesCount = Category::where('supplier_id', $supplier->sno)->count();

        return view('admin.suppliers.website.index', compact(
            'supplier',
            'hasCredentials',
            'connection',
            'localPublishedCount',
            'localCategoriesCount'
        ));
    }

    /**
     * Fetch products list directly from the supplier's WooCommerce store.
     */
    public function products(Request $request, $supplierId, WooCommerceService $wcService)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $result = $wcService->getStoreProducts($supplier, $request->all());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        $products = $result['products'];

        // Extract WooCommerce Product IDs to check mapping with PMS published_products
        $wcIds = array_filter(array_column($products, 'id'));

        $pmsMapped = [];
        if (!empty($wcIds)) {
            $publishedRecords = DB::table('published_products as pp')
                ->leftJoin('auto_designer_specification_master as dsm', 'dsm.sno', '=', 'pp.specification_id')
                ->where('pp.target_supplier_id', $supplier->sno)
                ->whereIn('pp.woocommerce_product_id', $wcIds)
                ->select([
                    'pp.sno as published_id',
                    'pp.woocommerce_product_id',
                    'pp.specification_id',
                    'pp.status as pms_status',
                    'dsm.sku as pms_sku',
                    'dsm.barcode as pms_barcode'
                ])
                ->get();

            foreach ($publishedRecords as $row) {
                $pmsMapped[$row->woocommerce_product_id] = $row;
            }
        }

        // Format and annotate each product
        foreach ($products as &$p) {
            $pId = $p['id'] ?? 0;
            $p['pms_linked'] = isset($pmsMapped[$pId]);
            $p['pms_info'] = $pmsMapped[$pId] ?? null;

            // Clean HTML entities (e.g. &amp; -> &) in product name
            if (!empty($p['name'])) {
                $p['name'] = html_entity_decode($p['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }

            // Extract primary image
            $imageSrc = null;
            if (!empty($p['images']) && is_array($p['images']) && isset($p['images'][0]['src'])) {
                $imageSrc = $p['images'][0]['src'];
            }
            $p['featured_image'] = $imageSrc;

            // Categories string
            $categoryNames = [];
            if (!empty($p['categories']) && is_array($p['categories'])) {
                foreach ($p['categories'] as $cat) {
                    if (isset($cat['name'])) {
                        $categoryNames[] = html_entity_decode($cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    }
                }
            }
            $p['category_names'] = implode(', ', $categoryNames);
        }

        return response()->json([
            'success' => true,
            'products' => $products,
            'total' => $result['total'],
            'total_pages' => $result['total_pages'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
        ]);
    }

    /**
     * Fetch categories list directly from the supplier's WooCommerce store.
     */
    public function categories(Request $request, $supplierId, WooCommerceService $wcService)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $result = $wcService->getStoreCategories($supplier, $request->all());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Clean HTML entities in category names & descriptions (e.g. &amp; -> &)
        if (!empty($result['categories']) && is_array($result['categories'])) {
            foreach ($result['categories'] as &$cat) {
                if (isset($cat['name'])) {
                    $cat['name'] = html_entity_decode($cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                if (isset($cat['description'])) {
                    $cat['description'] = html_entity_decode($cat['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        }

        return response()->json($result);
    }

    /**
     * Update WooCommerce product status (e.g. withdraw to 'draft' or publish).
     */
    public function updateProductStatus(Request $request, $supplierId, $wcProductId, WooCommerceService $wcService)
    {
        $request->validate([
            'status' => 'required|in:publish,draft,private,pending',
        ]);

        $supplier = Supplier::findOrFail($supplierId);
        $result = $wcService->updateProductStatus($supplier, (int) $wcProductId, $request->status);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Delete product from WooCommerce (either soft-delete to Trash or force permanent delete).
     */
    public function deleteProduct(Request $request, $supplierId, $wcProductId, WooCommerceService $wcService)
    {
        $supplier = Supplier::findOrFail($supplierId);
        $force = $request->boolean('force', false);

        $result = $wcService->deleteProduct($supplier, (int) $wcProductId, $force);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json($result);
    }

    /**
     * Create a new category on the supplier's WooCommerce store and sync locally.
     */
    public function storeCategory(Request $request, $supplierId, WooCommerceService $wcService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent' => 'nullable|integer',
            'description' => 'nullable|string',
        ]);

        $supplier = Supplier::findOrFail($supplierId);

        $result = $wcService->createStoreCategory($supplier, $request->all());

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        $wcCat = $result['category'] ?? [];
        $user = auth()->user();

        // Also save to PMS local categories table
        if (!empty($wcCat['id'])) {
            Category::updateOrCreate(
                [
                    'name' => $wcCat['name'],
                    'supplier_id' => $supplier->sno,
                ],
                [
                    'status' => 'active',
                    'countryid' => $user->country_id ?? null,
                    'companyid' => $user->company_id ?? null,
                    'subcompanyid' => $user->sub_company_id ?? null,
                    'projectid' => $user->project_id ?? null,
                    'subprojectid' => $user->sub_project_id ?? null,
                ]
            );
        }

        return response()->json($result);
    }

    /**
     * Delete a category on the supplier's WooCommerce store.
     */
    public function deleteCategory(Request $request, $supplierId, $wcCategoryId, WooCommerceService $wcService)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $result = $wcService->deleteStoreCategory($supplier, (int) $wcCategoryId, true);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        // Also clean up from local ERP categories if it was synced
        $deletedCatData = $result['data'] ?? [];
        if (!empty($deletedCatData['name'])) {
            Category::where('supplier_id', $supplier->sno)
                ->where('name', $deletedCatData['name'])
                ->delete();
        }

        return response()->json($result);
    }

    /**
     * Synchronize all WooCommerce categories from the store into the local PMS categories table.
     */
    public function syncCategories(Request $request, $supplierId, WooCommerceService $wcService)
    {
        $supplier = Supplier::findOrFail($supplierId);

        $result = $wcService->getStoreCategories($supplier, ['per_page' => 100]);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        $wcCategories = $result['categories'] ?? [];
        $syncedCount = 0;
        $user = auth()->user();

        // Map wc id to local category record for parent association
        $wcIdToLocalId = [];

        // 1. First pass: Create or update categories
        foreach ($wcCategories as $wcCat) {
            $local = Category::firstOrNew([
                'name' => trim($wcCat['name']),
                'supplier_id' => $supplier->sno,
            ]);

            $local->status = 'active';
            $local->countryid = $user->country_id ?? null;
            $local->companyid = $user->company_id ?? null;
            $local->subcompanyid = $user->sub_company_id ?? null;
            $local->projectid = $user->project_id ?? null;
            $local->subprojectid = $user->sub_project_id ?? null;
            $local->save();

            $wcIdToLocalId[$wcCat['id']] = $local->sno;
            $syncedCount++;
        }

        // 2. Second pass: Link parents
        foreach ($wcCategories as $wcCat) {
            if (!empty($wcCat['parent']) && isset($wcIdToLocalId[$wcCat['parent']]) && isset($wcIdToLocalId[$wcCat['id']])) {
                $childLocalId = $wcIdToLocalId[$wcCat['id']];
                $parentLocalId = $wcIdToLocalId[$wcCat['parent']];

                Category::where('sno', $childLocalId)->update([
                    'parent_id' => $parentLocalId
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully synchronized {$syncedCount} categories from WooCommerce store.",
            'synced_count' => $syncedCount,
        ]);
    }
}
