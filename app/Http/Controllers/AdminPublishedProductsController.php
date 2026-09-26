<?php

namespace App\Http\Controllers;

use App\Models\PublishedProductUpdateLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class AdminPublishedProductsController extends Controller
{
    /**
     * Display the Published Products list view.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Active Target Stores that have published products
        $targetStores = DB::table('published_products as pp')
            ->join('suppliers as s', 's.sno', '=', 'pp.target_supplier_id')
            ->select('s.sno', 's.name', 's.store_url', DB::raw('COUNT(pp.sno) as total_published'))
            ->groupBy('s.sno', 's.name', 's.store_url')
            ->orderBy('s.name', 'asc')
            ->get()
            ->map(function ($store) {
                $store->domain = parse_url($store->store_url, PHP_URL_HOST) ?: $store->store_url;
                return $store;
            });

        // All suppliers (for origin filter)
        $suppliers = DB::table('suppliers')->orderBy('name', 'asc')->get();

        // High level KPI metrics
        $totalPublished = DB::table('published_products')->count();
        $totalUniqueProducts = DB::table('published_products')->distinct('specification_id')->count('specification_id');
        $totalStores = $targetStores->count();

        return view('admin.published_products.index', compact(
            'targetStores',
            'suppliers',
            'totalPublished',
            'totalUniqueProducts',
            'totalStores'
        ));
    }

    /**
     * Fetch paginated JSON data for published products.
     */
    public function data(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 30, 50], true)) {
            $perPage = 20;
        }

        $search = trim($request->input('search', ''));
        $targetSupplierId = $request->input('target_supplier_id');
        $originSupplierId = $request->input('origin_supplier_id');

        $query = DB::table('published_products as pp')
            ->join('auto_designer_specification_master as dsm', 'dsm.sno', '=', 'pp.specification_id')
            ->leftJoin('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('suppliers as target_supplier', 'target_supplier.sno', '=', 'pp.target_supplier_id')
            ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'pp.origin_supplier_id')
            ->leftJoin('categories as cat', 'cat.sno', '=', 'pp.category_id')
            ->select([
                'pp.sno as published_id',
                'pp.specification_id',
                'pp.target_supplier_id',
                'pp.origin_supplier_id',
                'pp.woocommerce_product_id',
                'pp.permalink',
                'pp.category_name as published_category_name',
                'pp.status as publish_status',
                'pp.created_at as published_at',
                'pp.updated_at as last_updated_at',

                'dsm.sku',
                'dsm.barcode',
                'dsm.price',
                'dsm.sale_price',
                'itemtype.itemtype as product_type',

                DB::raw("COALESCE(NULLIF(aipd.AI_product_name, ''), itemname.itemname, 'Untitled Product') as product_name"),
                'aipd.AI_product_name',
                'aipd.AI_Producttag',

                'target_supplier.name as target_supplier_name',
                'target_supplier.store_url as target_store_url',
                'origin_supplier.name as origin_supplier_name',
                'cat.name as category_name'
            ]);

        // Filter by target supplier store
        if (!empty($targetSupplierId)) {
            $query->where('pp.target_supplier_id', $targetSupplierId);
        }

        // Filter by origin supplier
        if (!empty($originSupplierId)) {
            $query->where('pp.origin_supplier_id', $originSupplierId);
        }

        // Search
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('dsm.sku', 'like', "%{$search}%")
                  ->orWhere('dsm.barcode', 'like', "%{$search}%")
                  ->orWhere('aipd.AI_product_name', 'like', "%{$search}%")
                  ->orWhere('pp.category_name', 'like', "%{$search}%")
                  ->orWhere('pp.woocommerce_product_id', 'like', "%{$search}%")
                  ->orWhere('target_supplier.name', 'like', "%{$search}%")
                  ->orWhere('target_supplier.store_url', 'like', "%{$search}%")
                  ->orWhere('origin_supplier.name', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('pp.updated_at');

        $paginated = $query->paginate($perPage);

        // Fetch approved images for these specifications
        $specIds = collect($paginated->items())->pluck('specification_id')->unique()->toArray();

        $images = DB::table('approved_enhanced_images')
            ->whereIn('specification_id', $specIds)
            ->where('status', 'approved')
            ->whereIn('image_type', ['main', 'sub'])
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get()
            ->groupBy('specification_id');

        // Fetch all store publications for these specs to identify multi-store publishing
        $allStorePublications = DB::table('published_products as pp')
            ->join('suppliers as s', 's.sno', '=', 'pp.target_supplier_id')
            ->whereIn('pp.specification_id', $specIds)
            ->select([
                'pp.sno as published_id',
                'pp.specification_id',
                'pp.target_supplier_id',
                'pp.woocommerce_product_id',
                'pp.permalink',
                'pp.category_name',
                's.name as store_name',
                's.store_url'
            ])
            ->get()
            ->groupBy('specification_id');

        $items = collect($paginated->items())->map(function ($item) use ($images, $allStorePublications) {
            $specImages = $images->get($item->specification_id, collect());
            $mainImg = $specImages->firstWhere('image_type', 'main') ?? $specImages->first();
            $item->main_image = $mainImg ? $mainImg->enhanced_image_path : null;
            $item->total_images = $specImages->count();
            
            // Clean product title
            $item->clean_title = $this->cleanAiTitle($item->AI_product_name ?: $item->product_name);

            // Clean domain
            $item->target_domain = parse_url($item->target_store_url, PHP_URL_HOST) ?: $item->target_store_url;

            // Multi-store publications for this specification
            $pubs = $allStorePublications->get($item->specification_id, collect());
            $item->total_stores_count = $pubs->count();
            $item->other_stores = $pubs->where('published_id', '!=', $item->published_id)->map(function ($p) {
                $p->domain = parse_url($p->store_url, PHP_URL_HOST) ?: $p->store_url;
                return $p;
            })->values()->all();

            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem()
        ]);
    }

    /**
     * Display a comprehensive detail page for a published product.
     *
     * @param int $id Published product SNO or Specification SNO
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Try finding publication by published_products.sno
        $published = DB::table('published_products as pp')
            ->where('pp.sno', $id)
            ->first();

        // Fallback: if not found, check if $id is specification_id
        if (!$published) {
            $published = DB::table('published_products as pp')
                ->where('pp.specification_id', $id)
                ->orderByDesc('pp.updated_at')
                ->first();
        }

        if (!$published) {
            return redirect()->route('admin.published-products.index')
                ->with('error', 'Published product record not found.');
        }

        $specId = $published->specification_id;

        // 2. Fetch full specification with all master attributes & AI content
        $product = DB::table('auto_designer_specification_master as dsm')
            ->leftJoin('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
            ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
            ->leftJoin('auto_embellishment_master as embellishment', 'embellishment.id', '=', 'dsm.embellishment')
            ->leftJoin('auto_manufacturing_process_master as manufacturing', 'manufacturing.id', '=', 'dsm.manufacturing_process')
            ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'dsm.craftsman')
            ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'dsm.supplier_id')
            ->where('dsm.sno', $specId)
            ->select([
                'dsm.*',
                'dsm.sno as spec_id',
                'aipd.sno as ai_desc_id',
                'aipd.AI_product_name',
                'aipd.AI_product_description',
                'aipd.AI_Metatitle',
                'aipd.AI_Metakeywards',
                'aipd.AI_Metadescription',
                'aipd.AI_Producttag',
                'aipd.AI_Imagealttext',
                'itemname.itemname as master_product_name',
                'itemtype.itemtype as product_type',
                'gender.name as gender_name',
                'composition.composition_details as composition_name',
                'colour.colourname as colour_name',
                'size.size as size_name',
                'designer.designername as designer_name',
                'embellishment.embellishmentname as embellishment_name',
                'manufacturing.manufacturing_process as manufacturing_process_name',
                'craftsman.name as craftsman_name',
                'origin_supplier.name as origin_supplier_name'
            ])
            ->first();

        if (!$product) {
            return redirect()->route('admin.published-products.index')
                ->with('error', 'Product specification details not found.');
        }

        $product->clean_title = $this->cleanAiTitle($product->AI_product_name ?: $product->master_product_name);

        // 3. Target Supplier for this specific publication
        $targetSupplier = DB::table('suppliers')->where('sno', $published->target_supplier_id)->first();
        if ($targetSupplier) {
            $targetSupplier->domain = parse_url($targetSupplier->store_url, PHP_URL_HOST) ?: $targetSupplier->store_url;
        }

        // 4. Origin Supplier
        $originSupplier = DB::table('suppliers')->where('sno', $published->origin_supplier_id ?: $product->supplier_id)->first();

        // 5. Publisher user
        $publisher = $published->published_by ? DB::table('users')->where('id', $published->published_by)->first() : null;

        // 6. All publications across all stores for this product specification
        $allPublications = DB::table('published_products as pp')
            ->join('suppliers as s', 's.sno', '=', 'pp.target_supplier_id')
            ->leftJoin('users as u', 'u.id', '=', 'pp.published_by')
            ->where('pp.specification_id', $specId)
            ->select([
                'pp.*',
                's.name as store_name',
                's.store_url',
                'u.name as publisher_name'
            ])
            ->orderByDesc('pp.updated_at')
            ->get()
            ->map(function ($pub) {
                $pub->domain = parse_url($pub->store_url, PHP_URL_HOST) ?: $pub->store_url;
                return $pub;
            });

        // 7. Approved Images (main & sub)
        $approvedImages = DB::table('approved_enhanced_images')
            ->where('specification_id', $specId)
            ->where('status', 'approved')
            ->whereIn('image_type', ['main', 'sub'])
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get();

        // 8. Fetch current live stock from WooCommerce or fallback to ERP
        $currentLiveStock = null;
        if (!empty($published->woocommerce_product_id) && $targetSupplier && !empty($targetSupplier->consumer_key) && !empty($targetSupplier->consumer_secret)) {
            try {
                $wcRes = Http::withBasicAuth($targetSupplier->consumer_key, $targetSupplier->consumer_secret)
                    ->timeout(6)
                    ->get(rtrim($targetSupplier->store_url, '/') . '/wp-json/wc/v3/products/' . $published->woocommerce_product_id);
                if ($wcRes->successful()) {
                    $wcData = $wcRes->json();
                    $currentLiveStock = isset($wcData['stock_quantity']) ? (int) $wcData['stock_quantity'] : ($wcData['stock_status'] === 'instock' ? 1 : 0);
                }
            } catch (\Exception $e) {
                Log::warning("Could not query WC stock for product {$published->woocommerce_product_id}: " . $e->getMessage());
            }
        }

        if ($currentLiveStock === null) {
            // Fallback: vendor_stock_web
            $vswBaseQuery = DB::table('vendor_stock_web')
                ->where('item_id', $product->spec_id);
            $currentLiveStock = (clone $vswBaseQuery)->where(function($q) {
                $q->where('send_qty', 0)->orWhereNull('send_qty');
            })->where(function($q) {
                $q->where('avilable_qty', '>', 0)->orWhereNull('avilable_qty');
            })->count();
        }

        $product->current_live_stock = (int) $currentLiveStock;

        // 9. Stores available for publishing (that have store_url configured)
        $availableStores = DB::table('suppliers')
            ->whereNotNull('store_url')
            ->where('store_url', '!=', '')
            ->whereNotNull('consumer_key')
            ->where('consumer_key', '!=', '')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.published_products.show', compact(
            'published',
            'product',
            'targetSupplier',
            'originSupplier',
            'publisher',
            'allPublications',
            'approvedImages',
            'availableStores'
        ));
    }

    /**
     * Withdraw stock from WooCommerce store for this published product.
     */
    public function withdrawStock(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $request->validate([
            'withdraw_qty' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:500'
        ]);

        $published = DB::table('published_products')->where('sno', $id)->first();
        if (!$published) {
            return response()->json([
                'success' => false,
                'message' => 'Published product record not found.'
            ], 404);
        }

        $targetSupplier = DB::table('suppliers')->where('sno', $published->target_supplier_id)->first();
        if (!$targetSupplier || empty($targetSupplier->store_url) || empty($targetSupplier->consumer_key) || empty($targetSupplier->consumer_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Target supplier WooCommerce API credentials missing.'
            ], 400);
        }

        $storeUrl = rtrim($targetSupplier->store_url, '/');
        $wcProductEndpoint = $storeUrl . '/wp-json/wc/v3/products/' . $published->woocommerce_product_id;

        // 1. Fetch current live stock from WooCommerce
        try {
            $getRes = Http::withBasicAuth($targetSupplier->consumer_key, $targetSupplier->consumer_secret)
                ->timeout(15)
                ->get($wcProductEndpoint);

            if (!$getRes->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reach WooCommerce store to verify stock: ' . $getRes->body()
                ], 500);
            }

            $wcData = $getRes->json();
            $currentStock = isset($wcData['stock_quantity']) ? (int) $wcData['stock_quantity'] : 0;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error communicating with WooCommerce: ' . $e->getMessage()
            ], 500);
        }

        $withdrawQty = (int) $request->input('withdraw_qty');

        if ($currentStock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'This product is already out of stock (0 quantity) on WooCommerce.'
            ], 400);
        }

        if ($withdrawQty > $currentStock) {
            return response()->json([
                'success' => false,
                'message' => "Cannot withdraw {$withdrawQty} units. Only {$currentStock} unit(s) are currently available on the store."
            ], 422);
        }

        $newStock = max(0, $currentStock - $withdrawQty);
        $newStockStatus = $newStock > 0 ? 'instock' : 'outofstock';

        // 2. Update stock on WooCommerce via REST API
        try {
            $updateRes = Http::withBasicAuth($targetSupplier->consumer_key, $targetSupplier->consumer_secret)
                ->timeout(20)
                ->put($wcProductEndpoint, [
                    'manage_stock' => true,
                    'stock_quantity' => $newStock,
                    'stock_status' => $newStockStatus
                ]);

            if (!$updateRes->successful()) {
                $err = $updateRes->json();
                return response()->json([
                    'success' => false,
                    'message' => 'WooCommerce update failed: ' . ($err['message'] ?? $updateRes->body())
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Exception while updating WooCommerce stock: ' . $e->getMessage()
            ], 500);
        }

        // 3. Update database / ERP tracking
        try {
            DB::beginTransaction();

            // Touch published_products
            DB::table('published_products')
                ->where('sno', $published->sno)
                ->update(['updated_at' => now()]);

            // Adjust unallocated rows in vendor_stock if present
            $spec = DB::table('auto_designer_specification_master')->where('sno', $published->specification_id)->first();
            if ($spec) {
                $rowsToDelete = DB::table('vendor_stock')
                    ->where(function($q) use ($spec) {
                        $q->where('item_id', $spec->sno);
                        if (!empty($spec->barcode)) {
                            $q->orWhere('barcode', $spec->barcode);
                        }
                    })
                    ->where(function($q) {
                        $q->where('send_qty', 0)->orWhereNull('send_qty');
                    })
                    ->whereNull('orderid')
                    ->orderBy('sno', 'desc')
                    ->limit($withdrawQty)
                    ->pluck('sno');

                if ($rowsToDelete->isNotEmpty()) {
                    DB::table('vendor_stock')->whereIn('sno', $rowsToDelete)->delete();
                }

                // Update supplier_products stock if exists
                if (!empty($spec->supplier_product_id)) {
                    DB::table('supplier_products')
                        ->where('sno', $spec->supplier_product_id)
                        ->update(['stock' => $newStock, 'updated_at' => now()]);
                }
            }

            // Write audit log if model exists
            try {
                PublishedProductUpdateLog::create([
                    'published_product_id' => $published->sno,
                    'specification_id' => $published->specification_id,
                    'woocommerce_product_id' => $published->woocommerce_product_id,
                    'target_supplier_id' => $published->target_supplier_id,
                    'sku' => $spec->sku ?? '',
                    'barcode' => $spec->barcode ?? null,
                    'old_stock' => $currentStock,
                    'new_stock' => $newStock,
                    'source_channel' => 'other',
                    'reason_notes' => 'Stock withdrawal: ' . ($request->input('reason') ?: "Withdrew {$withdrawQty} unit(s) from WooCommerce"),
                    'sync_status' => 'success',
                    'updated_by' => $user->id,
                    'updated_by_name' => $user->name ?? $user->username ?? 'Admin',
                ]);
            } catch (\Exception $eLog) {
                Log::warning('Stock log error: ' . $eLog->getMessage());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning('ERP stock adjustment error after WC stock withdrawal: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully withdrew {$withdrawQty} " . ($withdrawQty > 1 ? 'quantities' : 'quantity') . " from {$targetSupplier->name}. Current live stock on WooCommerce is now {$newStock}.",
            'withdrawn_qty' => $withdrawQty,
            'new_stock' => $newStock,
            'stock_status' => $newStockStatus
        ]);
    }


    /**
     * Remove / unpublish a published product record from the ERP registry.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $record = DB::table('published_products')->where('sno', $id)->first();
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Published product record not found.'
            ], 404);
        }

        DB::table('published_products')->where('sno', $id)->delete();

        // If no other publications exist for this spec, update status in dsm
        $remaining = DB::table('published_products')->where('specification_id', $record->specification_id)->count();
        if ($remaining === 0) {
            DB::table('auto_designer_specification_master')
                ->where('sno', $record->specification_id)
                ->update(['status' => 'Approved']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Publication record removed successfully.'
        ]);
    }

    /**
     * Helper to clean AI product title strings.
     */
    private function cleanAiTitle($title)
    {
        if (empty($title)) return '';
        $clean = trim($title);
        if (preg_match('/^(.*?)(?:\*\*Meta|\*\*Description|\*\*Tag|Meta Tag|Meta:)/i', $clean, $m)) {
            $clean = trim($m[1]);
        }
        $clean = preg_replace('/^\*+|\*+$/', '', $clean);
        return trim($clean, " \t\n\r\0\x0B*:-");
    }
}
