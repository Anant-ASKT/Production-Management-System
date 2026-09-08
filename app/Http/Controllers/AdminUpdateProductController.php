<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PublishedProductUpdateLog;
use App\Services\WooCommerceService;

class AdminUpdateProductController extends Controller
{
    /**
     * Display the Update Product management view.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Summary counts
        $totalPublished = DB::table('published_products')->count();
        $totalLogs = PublishedProductUpdateLog::count();
        $whatsappLogs = PublishedProductUpdateLog::where('source_channel', 'whatsapp')->count();
        $phoneLogs = PublishedProductUpdateLog::where('source_channel', 'phone_call')->count();

        // Suppliers for filtering
        $suppliers = DB::table('suppliers')->orderBy('name', 'asc')->get();

        return view('admin.update_products.index', compact(
            'totalPublished',
            'totalLogs',
            'whatsappLogs',
            'phoneLogs',
            'suppliers'
        ));
    }

    /**
     * Fetch paginated JSON data for published products ready for stock/price updating.
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

        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50, 100], true)) {
            $perPage = 15;
        }

        $search = trim($request->input('search', ''));
        $targetSupplierId = $request->input('target_supplier_id');
        $stockFilter = $request->input('stock_filter', 'all');

        $query = DB::table('published_products as pp')
            ->join('auto_designer_specification_master as dsm', 'dsm.sno', '=', 'pp.specification_id')
            ->leftJoin('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('suppliers as target_supplier', 'target_supplier.sno', '=', 'pp.target_supplier_id')
            ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'pp.origin_supplier_id')
            ->select([
                'pp.sno as published_id',
                'pp.specification_id',
                'pp.woocommerce_product_id',
                'pp.permalink',
                'pp.category_name as published_category_name',
                'pp.status as publish_status',
                'pp.target_supplier_id',
                'pp.created_at as published_at',
                'pp.updated_at as last_updated_at',

                'dsm.sku',
                'dsm.sku_supplier',
                'dsm.barcode',
                'dsm.supplier_product_id',
                'dsm.price as spec_price',
                'dsm.sale_price as spec_sale_price',
                'dsm.min_price as spec_min_price',

                'itemtype.itemtype as product_type',
                DB::raw("COALESCE(NULLIF(aipd.AI_product_name, ''), itemname.itemname, 'Untitled Product') as product_name"),
                'aipd.AI_product_name',

                'target_supplier.name as target_supplier_name',
                'target_supplier.store_url as target_store_url',
                'origin_supplier.name as origin_supplier_name',
            ]);

        if (!empty($targetSupplierId)) {
            $query->where('pp.target_supplier_id', $targetSupplierId);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('dsm.sku', 'like', "%{$search}%")
                  ->orWhere('dsm.sku_supplier', 'like', "%{$search}%")
                  ->orWhere('dsm.barcode', 'like', "%{$search}%")
                  ->orWhere('aipd.AI_product_name', 'like', "%{$search}%")
                  ->orWhere('itemname.itemname', 'like', "%{$search}%")
                  ->orWhere('target_supplier.name', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('pp.updated_at');

        $paginated = $query->paginate($perPage);

        // Fetch images for these specifications
        $specIds = collect($paginated->items())->pluck('specification_id')->unique()->toArray();
        $images = DB::table('approved_enhanced_images')
            ->whereIn('specification_id', $specIds)
            ->where('status', 'approved')
            ->orderByRaw("FIELD(image_type, 'main', 'sub')")
            ->orderBy('sno', 'asc')
            ->get()
            ->groupBy('specification_id');

        // Fetch latest update log for each published product
        $publishedIds = collect($paginated->items())->pluck('published_id')->toArray();
        $latestLogs = PublishedProductUpdateLog::whereIn('published_product_id', $publishedIds)
            ->orderByDesc('sno')
            ->get()
            ->groupBy('published_product_id');

        // Map items with pricing and available stock
        $items = collect($paginated->items())->map(function ($item) use ($images, $latestLogs) {
            $specImages = $images->get($item->specification_id, collect());
            $mainImg = $specImages->firstWhere('image_type', 'main') ?? $specImages->first();
            $item->image_url = $mainImg ? $mainImg->enhanced_image_path : null;

            $item->clean_title = $this->cleanAiTitle($item->AI_product_name ?: $item->product_name);
            $item->product_name = $item->clean_title ?: $item->product_name;

            // Fetch supplier product pricing/stock fallback
            $sp = null;
            if (!empty($item->supplier_product_id)) {
                $sp = DB::table('supplier_products')->where('sno', $item->supplier_product_id)->first();
            }

            $regularPrice = !empty($item->spec_price) ? (float) $item->spec_price : (!empty($sp->price) ? (float) $sp->price : 0);
            $salePrice = !empty($item->spec_sale_price) ? (float) $item->spec_sale_price : (!empty($sp->sale_price) ? (float) $sp->sale_price : null);

            // Compute current ERP available stock in vendor_stock
            $erpStock = DB::table('vendor_stock')
                ->where(function($q) use ($item) {
                    $q->where('item_id', $item->specification_id);
                    if (!empty($item->barcode)) {
                        $q->orWhere('barcode', $item->barcode);
                    }
                })
                ->where(function($q) {
                    $q->where('send_qty', 0)->orWhereNull('send_qty');
                })
                ->where(function($q) {
                    $q->where('avilable_qty', '>', 0)->orWhereNull('avilable_qty');
                })
                ->count();

            // If no vendor_stock rows, fallback to supplier_products stock
            $stockQty = $erpStock > 0 ? $erpStock : (isset($sp->stock) ? (int) $sp->stock : 0);

            $item->current_regular_price = $regularPrice;
            $item->current_sale_price = $salePrice;
            $item->current_stock = $stockQty;
            $item->erp_available_stock = $erpStock;

            // Latest update log info
            $productLogs = $latestLogs->get($item->published_id, collect());
            $lastLog = $productLogs->first();
            $item->last_log = $lastLog ? [
                'updated_at' => $lastLog->created_at->format('d M Y, h:i A'),
                'channel' => $lastLog->source_channel,
                'updated_by' => $lastLog->updated_by_name,
                'notes' => $lastLog->reason_notes,
                'sync_status' => $lastLog->sync_status,
            ] : null;

            $item->total_updates_count = $productLogs->count();

            return $item;
        });

        // Apply in-memory stock filter if needed
        if ($stockFilter === 'in_stock') {
            $items = $items->filter(fn($i) => $i->current_stock > 0)->values();
        } elseif ($stockFilter === 'out_of_stock') {
            $items = $items->filter(fn($i) => $i->current_stock <= 0)->values();
        } elseif ($stockFilter === 'low_stock') {
            $items = $items->filter(fn($i) => $i->current_stock > 0 && $i->current_stock <= 5)->values();
        }

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
     * Update product price and stock on WooCommerce, in the local ERP, and write an audit log.
     */
    public function update(Request $request, $id, WooCommerceService $wcService)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $request->validate([
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'source_channel' => 'required|in:whatsapp,phone_call,email,in_person,other',
            'reason_notes' => 'nullable|string|max:1000',
            'sync_woocommerce' => 'nullable|boolean',
        ]);

        $published = DB::table('published_products')->where('sno', $id)->first();
        if (!$published) {
            return response()->json([
                'success' => false,
                'message' => 'Published product record not found.'
            ], 404);
        }

        $spec = DB::table('auto_designer_specification_master')->where('sno', $published->specification_id)->first();
        if (!$spec) {
            return response()->json([
                'success' => false,
                'message' => 'Product specification not found.'
            ], 404);
        }

        $newRegularPrice = round((float) $request->input('regular_price'), 2);
        $newSalePrice = $request->filled('sale_price') ? round((float) $request->input('sale_price'), 2) : null;
        $newStock = (int) $request->input('stock_quantity');
        $sourceChannel = $request->input('source_channel');
        $reasonNotes = trim($request->input('reason_notes', ''));
        $shouldSyncWc = $request->boolean('sync_woocommerce', true);

        // Fetch old values
        $oldRegularPrice = !empty($spec->price) ? (float) $spec->price : null;
        $oldSalePrice = !empty($spec->sale_price) ? (float) $spec->sale_price : null;

        // Current ERP available stock
        $oldStock = DB::table('vendor_stock')
            ->where(function($q) use ($spec) {
                $q->where('item_id', $spec->sno);
                if (!empty($spec->barcode)) {
                    $q->orWhere('barcode', $spec->barcode);
                }
            })
            ->where(function($q) {
                $q->where('send_qty', 0)->orWhereNull('send_qty');
            })
            ->where(function($q) {
                $q->where('avilable_qty', '>', 0)->orWhereNull('avilable_qty');
            })
            ->count();

        // 1. Sync to WooCommerce Store (if enabled)
        $wcResult = null;
        $syncStatus = 'local_only';
        $syncError = null;

        if ($shouldSyncWc && !empty($published->woocommerce_product_id)) {
            $wcResult = $wcService->updateProductPriceAndStock((int) $id, [
                'regular_price' => $newRegularPrice,
                'sale_price' => $newSalePrice,
                'stock_quantity' => $newStock,
            ]);

            if (!empty($wcResult['success'])) {
                $syncStatus = 'success';
            } else {
                $syncStatus = 'failed';
                $syncError = $wcResult['message'] ?? 'WooCommerce sync failed.';
            }
        }

        // 2. Update Local ERP Specifications & Supplier Product
        try {
            DB::beginTransaction();

            // Update auto_designer_specification_master
            DB::table('auto_designer_specification_master')
                ->where('sno', $spec->sno)
                ->update([
                    'price' => $newRegularPrice,
                    'sale_price' => $newSalePrice,
                    'edatetime' => now(),
                ]);

            // Update supplier_products if linked
            if (!empty($spec->supplier_product_id)) {
                DB::table('supplier_products')
                    ->where('sno', $spec->supplier_product_id)
                    ->update([
                        'price' => $newRegularPrice,
                        'sale_price' => $newSalePrice,
                        'stock' => $newStock,
                        'updated_at' => now(),
                    ]);
            }

            // Adjust vendor_stock rows to match exact new stock
            $currentAvailableRows = DB::table('vendor_stock')
                ->where(function($q) use ($spec) {
                    $q->where('item_id', $spec->sno);
                    if (!empty($spec->barcode)) {
                        $q->orWhere('barcode', $spec->barcode);
                    }
                })
                ->where(function($q) {
                    $q->where('send_qty', 0)->orWhereNull('send_qty');
                })
                ->where(function($q) {
                    $q->where('avilable_qty', '>', 0)->orWhereNull('avilable_qty');
                })
                ->orderBy('sno', 'desc')
                ->get();

            $currentAvailableCount = $currentAvailableRows->count();

            if ($newStock > $currentAvailableCount) {
                // Insert additional available rows
                $unitsToAdd = $newStock - $currentAvailableCount;
                $maxStockId = (int) DB::table('vendor_stock')->max('id');

                $newRows = [];
                for ($i = 0; $i < $unitsToAdd; $i++) {
                    $newRows[] = [
                        'id' => ++$maxStockId,
                        'companyid' => $spec->companyid ?: ($user->companyid ?? 14),
                        'subcompanyid' => $spec->subcompanyid ?: ($user->subcompanyid ?? 7),
                        'projectid' => $spec->projectid ?: ($user->projectid ?? 9),
                        'vendor_id' => $spec->supplier_id ?: 2,
                        'item_id' => $spec->id ?: $spec->sno,
                        'g_id' => $spec->id ?: $spec->sno,
                        'barcode' => $spec->barcode,
                        'quantity_received' => 1,
                        'send_qty' => 0,
                        'sale_price' => $newSalePrice ?: $newRegularPrice,
                        'stock_date' => now()->toDateString(),
                        'stockentrydate' => now()->toDateString(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('vendor_stock')->insert($newRows);
            } elseif ($newStock < $currentAvailableCount) {
                // Delete unallocated excess rows (where send_qty == 0 and orderid is null)
                $unitsToRemove = $currentAvailableCount - $newStock;
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
                    ->limit($unitsToRemove)
                    ->pluck('sno');

                if ($rowsToDelete->isNotEmpty()) {
                    DB::table('vendor_stock')->whereIn('sno', $rowsToDelete)->delete();
                }
            }

            // Update sale price on all existing unallocated units
            DB::table('vendor_stock')
                ->where(function($q) use ($spec) {
                    $q->where('item_id', $spec->sno);
                    if (!empty($spec->barcode)) {
                        $q->orWhere('barcode', $spec->barcode);
                    }
                })
                ->where(function($q) {
                    $q->where('send_qty', 0)->orWhereNull('send_qty');
                })
                ->update([
                    'sale_price' => $newSalePrice ?: $newRegularPrice,
                    'updated_at' => now(),
                ]);

            // Touch published_products updated_at
            DB::table('published_products')
                ->where('sno', $published->sno)
                ->update(['updated_at' => now()]);

            // 3. Write Audit Log
            $log = PublishedProductUpdateLog::create([
                'published_product_id' => $published->sno,
                'specification_id' => $spec->sno,
                'woocommerce_product_id' => $published->woocommerce_product_id,
                'target_supplier_id' => $published->target_supplier_id,
                'sku' => $spec->sku,
                'barcode' => $spec->barcode,
                'old_regular_price' => $oldRegularPrice,
                'new_regular_price' => $newRegularPrice,
                'old_sale_price' => $oldSalePrice,
                'new_sale_price' => $newSalePrice,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'source_channel' => $sourceChannel,
                'reason_notes' => $reasonNotes,
                'sync_status' => $syncStatus,
                'sync_error' => $syncError,
                'sync_payload' => !empty($wcResult['payload']) ? json_encode($wcResult['payload']) : null,
                'sync_response' => !empty($wcResult['data']) ? json_encode($wcResult['data']) : (!empty($wcResult['response']) ? (string)$wcResult['response'] : null),
                'updated_by' => $user->id,
                'updated_by_name' => $user->name ?? $user->username ?? 'Admin',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product stock and price updated successfully.' . ($syncStatus === 'success' ? ' Synced with WordPress.' : ($syncStatus === 'failed' ? ' (Notice: WordPress sync error: ' . $syncError . ')' : '')),
                'sync_status' => $syncStatus,
                'data' => [
                    'regular_price' => $newRegularPrice,
                    'sale_price' => $newSalePrice,
                    'stock' => $newStock,
                    'log_id' => $log->sno,
                    'updated_at' => now()->format('d M Y, h:i A')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product stock/price: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Database update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch paginated audit logs for all product updates.
     */
    public function logs(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $perPage = (int) $request->input('per_page', 20);
        $search = trim($request->input('search', ''));
        $channel = $request->input('channel', '');

        $query = PublishedProductUpdateLog::query();

        if (!empty($channel) && $channel !== 'all') {
            $query->where('source_channel', $channel);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('reason_notes', 'like', "%{$search}%")
                  ->orWhere('updated_by_name', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('sno');

        $paginated = $query->paginate($perPage);

        $items = collect($paginated->items())->map(function ($log) {
            return [
                'sno' => $log->sno,
                'sku' => $log->sku,
                'barcode' => $log->barcode,
                'old_regular_price' => $log->old_regular_price,
                'new_regular_price' => $log->new_regular_price,
                'old_sale_price' => $log->old_sale_price,
                'new_sale_price' => $log->new_sale_price,
                'old_stock' => $log->old_stock,
                'new_stock' => $log->new_stock,
                'source_channel' => $log->source_channel,
                'reason_notes' => $log->reason_notes,
                'sync_status' => $log->sync_status,
                'sync_error' => $log->sync_error,
                'updated_by_name' => $log->updated_by_name,
                'created_at' => $log->created_at ? $log->created_at->format('d M Y, h:i A') : '—',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
        ]);
    }

    /**
     * Fetch logs specifically for a single published product.
     */
    public function productLogs($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $logs = PublishedProductUpdateLog::where('published_product_id', $id)
            ->orWhere('specification_id', $id)
            ->orderByDesc('sno')
            ->get()
            ->map(function ($log) {
                return [
                    'sno' => $log->sno,
                    'sku' => $log->sku,
                    'barcode' => $log->barcode,
                    'old_regular_price' => $log->old_regular_price,
                    'new_regular_price' => $log->new_regular_price,
                    'old_sale_price' => $log->old_sale_price,
                    'new_sale_price' => $log->new_sale_price,
                    'old_stock' => $log->old_stock,
                    'new_stock' => $log->new_stock,
                    'source_channel' => $log->source_channel,
                    'reason_notes' => $log->reason_notes,
                    'sync_status' => $log->sync_status,
                    'sync_error' => $log->sync_error,
                    'updated_by_name' => $log->updated_by_name,
                    'created_at' => $log->created_at ? $log->created_at->format('d M Y, h:i A') : '—',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }

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
