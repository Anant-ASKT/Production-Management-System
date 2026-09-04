<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWebhookPayload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminWebsiteOrderController extends Controller
{
    /**
     * Display the Website Orders list view.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Summary counts
        $totalOrders = OrderWebhookPayload::count();
        $processingOrders = OrderWebhookPayload::where('status', 'processing')->count();
        $completedOrders = OrderWebhookPayload::where('status', 'completed')->count();
        $pendingOrders = OrderWebhookPayload::whereIn('status', ['pending', 'on-hold'])->count();

        // Suppliers list for filtering if needed
        $suppliers = DB::table('suppliers')->orderBy('name', 'asc')->get();

        return view('admin.website_orders.index', compact(
            'totalOrders',
            'processingOrders',
            'completedOrders',
            'pendingOrders',
            'suppliers'
        ));
    }

    /**
     * Fetch paginated and filtered JSON data for Website Orders.
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
        if (!in_array($perPage, [10, 20, 30, 50, 100], true)) {
            $perPage = 20;
        }

        $search = trim($request->input('search', ''));
        $status = trim($request->input('status', ''));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = OrderWebhookPayload::query();

        // Filter by Status
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by Date Range
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Search across fields
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('order_key', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('payload->number', 'like', "%{$search}%")
                  ->orWhere('payload->billing->first_name', 'like', "%{$search}%")
                  ->orWhere('payload->billing->last_name', 'like', "%{$search}%")
                  ->orWhere('payload->billing->email', 'like', "%{$search}%")
                  ->orWhere('payload->billing->phone', 'like', "%{$search}%")
                  ->orWhere('payload->shipping->first_name', 'like', "%{$search}%")
                  ->orWhere('payload->shipping->last_name', 'like', "%{$search}%")
                  ->orWhere('payload->payment_method_title', 'like', "%{$search}%")
                  ->orWhere('payload', 'like', "%{$search}%");
            });
        }

        $query->orderByDesc('id');

        $paginated = $query->paginate($perPage);

        // Fetch all suppliers for store URL matching
        $allSuppliers = DB::table('suppliers')->get();

        // Collect all SKUs and WC product IDs across the page items
        $collectedSkus = [];
        $collectedWcProductIds = [];

        foreach ($paginated->items() as $record) {
            $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
            foreach ($payload['line_items'] ?? [] as $item) {
                if (!empty($item['sku'])) {
                    $collectedSkus[] = trim($item['sku']);
                }
                if (!empty($item['product_id'])) {
                    $collectedWcProductIds[] = (int) $item['product_id'];
                }
            }
        }

        $specLookup = $this->buildSpecificationLookup(array_unique($collectedSkus), array_unique($collectedWcProductIds));

        // Transform records into friendly display format
        $items = collect($paginated->items())->map(function ($record) use ($allSuppliers, $specLookup) {
            $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
            $headers = is_array($record->headers) ? $record->headers : (json_decode($record->headers, true) ?? []);

            $billing = $payload['billing'] ?? [];
            $shipping = $payload['shipping'] ?? [];
            $lineItems = $payload['line_items'] ?? [];

            $customerName = trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''));
            if (empty($customerName)) {
                $customerName = trim(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? ''));
            }
            if (empty($customerName)) {
                $customerName = 'Guest Customer';
            }

            $customerEmail = $billing['email'] ?? ($payload['customer_email'] ?? '—');
            $customerPhone = $billing['phone'] ?? ($shipping['phone'] ?? '—');
            $customerCity = $billing['city'] ?? ($shipping['city'] ?? '');
            $customerState = $billing['state'] ?? ($shipping['state'] ?? '');
            $customerCountry = $billing['country'] ?? ($shipping['country'] ?? '');

            $currencySymbol = $payload['currency_symbol'] ?? '₹';
            $currency = $payload['currency'] ?? 'INR';
            $total = isset($payload['total']) ? (float) $payload['total'] : 0.00;
            $paymentMethod = $payload['payment_method_title'] ?? ($payload['payment_method'] ?? 'N/A');

            // Detect selling supplier / store
            $storeInfo = $this->resolveSellingSupplier($headers, $allSuppliers);

            // Format & enrich line items
            $itemsSummary = [];
            foreach ($lineItems as $item) {
                $sku = trim($item['sku'] ?? '');
                $wcPid = (int) ($item['product_id'] ?? 0);
                $specData = $specLookup['by_sku'][$sku] ?? ($specLookup['by_wc_id'][$wcPid] ?? null);

                $imgSrc = $specData['enhanced_image'] ?? ($item['image']['src'] ?? null);

                $itemsSummary[] = [
                    'id' => $item['id'] ?? null,
                    'name' => $item['name'] ?? 'Product',
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => isset($item['price']) ? (float) $item['price'] : 0,
                    'total' => isset($item['total']) ? (float) $item['total'] : 0,
                    'sku' => !empty($sku) ? $sku : ($specData['sku'] ?? '—'),
                    'image' => $imgSrc,
                    // Linked PMS Details
                    'is_matched' => !empty($specData),
                    'spec_id' => $specData['spec_id'] ?? null,
                    'spec_url' => !empty($specData['spec_id']) ? route('admin.publish-products.show', $specData['spec_id']) : null,
                    'origin_supplier_id' => $specData['origin_supplier_id'] ?? null,
                    'origin_supplier_name' => $specData['origin_supplier_name'] ?? '—',
                    'origin_supplier_url' => $specData['origin_supplier_url'] ?? null,
                    'product_type' => $specData['product_type'] ?? null,
                    'colour' => $specData['colour'] ?? null,
                    'size' => $specData['size'] ?? null,
                    'composition' => $specData['composition'] ?? null,
                    'spec_price' => $specData['price'] ?? null,
                    'spec_sale_price' => $specData['sale_price'] ?? null,
                    'spec_min_price' => $specData['min_price'] ?? null,
                ];
            }

            return [
                'id' => $record->id,
                'order_id' => $record->order_id ?: ($payload['number'] ?? ('#' . $record->id)),
                'order_number' => $payload['number'] ?? $record->order_id ?? ('#' . $record->id),
                'order_key' => $record->order_key ?: ($payload['order_key'] ?? '—'),
                'status' => strtolower($record->status ?: ($payload['status'] ?? 'pending')),
                'created_at_formatted' => $record->created_at ? $record->created_at->format('d M Y, h:i A') : '—',
                'order_date' => !empty($payload['date_created']) ? date('d M Y, h:i A', strtotime($payload['date_created'])) : ($record->created_at ? $record->created_at->format('d M Y, h:i A') : '—'),
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'customer_location' => trim("{$customerCity} {$customerState} {$customerCountry}"),
                'currency_symbol' => $currencySymbol,
                'currency' => $currency,
                'total' => $total,
                'total_formatted' => $currencySymbol . number_format($total, 2),
                'payment_method' => $paymentMethod,
                'items_count' => count($lineItems),
                'items_summary' => $itemsSummary,
                'selling_supplier_name' => $storeInfo['name'],
                'selling_supplier_url' => $storeInfo['store_url'],
                'source_store' => $storeInfo['domain'],
            ];
        });

        // Compute fresh overall statistics
        $stats = [
            'total_orders' => OrderWebhookPayload::count(),
            'processing_orders' => OrderWebhookPayload::where('status', 'processing')->count(),
            'completed_orders' => OrderWebhookPayload::where('status', 'completed')->count(),
            'pending_orders' => OrderWebhookPayload::whereIn('status', ['pending', 'on-hold'])->count(),
            'cancelled_orders' => OrderWebhookPayload::whereIn('status', ['cancelled', 'failed', 'refunded'])->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $items,
            'stats' => $stats,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'from' => $paginated->firstItem(),
            'to' => $paginated->lastItem()
        ]);
    }

    /**
     * Get single Order Detail modal data with full product & supplier mapping.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $record = OrderWebhookPayload::findOrFail($id);
        $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
        $headers = is_array($record->headers) ? $record->headers : (json_decode($record->headers, true) ?? []);

        $allSuppliers = DB::table('suppliers')->get();
        $storeInfo = $this->resolveSellingSupplier($headers, $allSuppliers);

        $lineItems = $payload['line_items'] ?? [];
        $skus = [];
        $wcProductIds = [];

        foreach ($lineItems as $item) {
            if (!empty($item['sku'])) $skus[] = trim($item['sku']);
            if (!empty($item['product_id'])) $wcProductIds[] = (int) $item['product_id'];
        }

        $specLookup = $this->buildSpecificationLookup(array_unique($skus), array_unique($wcProductIds));

        // Enrich line items with full specification & origin supplier details
        $enrichedLineItems = [];
        foreach ($lineItems as $item) {
            $sku = trim($item['sku'] ?? '');
            $wcPid = (int) ($item['product_id'] ?? 0);
            $specData = $specLookup['by_sku'][$sku] ?? ($specLookup['by_wc_id'][$wcPid] ?? null);

            $imgSrc = $specData['enhanced_image'] ?? ($item['image']['src'] ?? null);

            $enrichedLineItems[] = array_merge($item, [
                'resolved_sku' => !empty($sku) ? $sku : ($specData['sku'] ?? '—'),
                'resolved_image' => $imgSrc,
                'is_matched' => !empty($specData),
                'spec_id' => $specData['spec_id'] ?? null,
                'spec_url' => !empty($specData['spec_id']) ? route('admin.publish-products.show', $specData['spec_id']) : null,
                'barcode' => $specData['barcode'] ?? null,
                'origin_supplier_id' => $specData['origin_supplier_id'] ?? null,
                'origin_supplier_name' => $specData['origin_supplier_name'] ?? '—',
                'origin_supplier_url' => $specData['origin_supplier_url'] ?? null,
                'product_type' => $specData['product_type'] ?? null,
                'colour' => $specData['colour'] ?? null,
                'size' => $specData['size'] ?? null,
                'composition' => $specData['composition'] ?? null,
                'gender' => $specData['gender'] ?? null,
                'designer' => $specData['designer'] ?? null,
                'craftsman' => $specData['craftsman'] ?? null,
                'master_product_name' => $specData['master_product_name'] ?? null,
                'ai_product_name' => $specData['ai_product_name'] ?? null,
                'spec_price' => $specData['price'] ?? null,
                'spec_sale_price' => $specData['sale_price'] ?? null,
                'spec_min_price' => $specData['min_price'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'record_id' => $record->id,
                'order_id' => $record->order_id ?: ($payload['number'] ?? ('#' . $record->id)),
                'order_number' => $payload['number'] ?? $record->order_id ?? ('#' . $record->id),
                'order_key' => $record->order_key ?: ($payload['order_key'] ?? '—'),
                'status' => strtolower($record->status ?: ($payload['status'] ?? 'pending')),
                'created_at' => $record->created_at ? $record->created_at->format('d M Y, h:i A') : '—',
                'date_created' => !empty($payload['date_created']) ? date('d M Y, h:i A', strtotime($payload['date_created'])) : '—',
                'date_paid' => !empty($payload['date_paid']) ? date('d M Y, h:i A', strtotime($payload['date_paid'])) : '—',
                'date_completed' => !empty($payload['date_completed']) ? date('d M Y, h:i A', strtotime($payload['date_completed'])) : '—',
                'currency_symbol' => $payload['currency_symbol'] ?? '₹',
                'currency' => $payload['currency'] ?? 'INR',
                'total' => isset($payload['total']) ? (float) $payload['total'] : 0,
                'discount_total' => isset($payload['discount_total']) ? (float) $payload['discount_total'] : 0,
                'shipping_total' => isset($payload['shipping_total']) ? (float) $payload['shipping_total'] : 0,
                'total_tax' => isset($payload['total_tax']) ? (float) $payload['total_tax'] : 0,
                'payment_method' => $payload['payment_method_title'] ?? ($payload['payment_method'] ?? 'N/A'),
                'transaction_id' => $payload['transaction_id'] ?? '—',
                'customer_ip_address' => $payload['customer_ip_address'] ?? '—',
                'customer_note' => $payload['customer_note'] ?? '',
                'billing' => $payload['billing'] ?? [],
                'shipping' => $payload['shipping'] ?? [],
                'line_items' => $enrichedLineItems,
                'shipping_lines' => $payload['shipping_lines'] ?? [],
                'tax_lines' => $payload['tax_lines'] ?? [],
                'fee_lines' => $payload['fee_lines'] ?? [],
                'coupon_lines' => $payload['coupon_lines'] ?? [],
                'selling_supplier_name' => $storeInfo['name'],
                'selling_supplier_url' => $storeInfo['store_url'],
                'source_store' => $storeInfo['domain'],
                'headers' => $headers,
                'raw_payload' => $payload,
            ]
        ]);
    }

    /**
     * Resolve the selling supplier store from webhook headers.
     */
    private function resolveSellingSupplier($headers, $allSuppliers)
    {
        $sourceUrl = $headers['x-wc-webhook-source'][0] ?? ($headers['host'][0] ?? '');
        $cleanSource = preg_replace('#^https?://#i', '', rtrim(trim($sourceUrl), '/'));

        if (!empty($cleanSource)) {
            foreach ($allSuppliers as $sup) {
                $cleanSupUrl = preg_replace('#^https?://#i', '', rtrim(trim($sup->store_url ?? ''), '/'));
                if (!empty($cleanSupUrl) && (str_contains($cleanSource, $cleanSupUrl) || str_contains($cleanSupUrl, $cleanSource))) {
                    return [
                        'supplier_id' => $sup->sno,
                        'name' => $sup->name,
                        'store_url' => $sup->store_url,
                        'domain' => $cleanSupUrl
                    ];
                }
            }
        }

        return [
            'supplier_id' => null,
            'name' => !empty($cleanSource) ? $cleanSource : 'Website Store',
            'store_url' => $sourceUrl,
            'domain' => !empty($cleanSource) ? $cleanSource : 'Website Store'
        ];
    }

    /**
     * Bulk build specification lookup table indexed by SKU and by WooCommerce product ID.
     */
    private function buildSpecificationLookup(array $skus, array $wcProductIds)
    {
        $bySku = [];
        $byWcId = [];

        if (empty($skus) && empty($wcProductIds)) {
            return ['by_sku' => [], 'by_wc_id' => []];
        }

        // Query by SKUs
        if (!empty($skus)) {
            $specRows = DB::table('auto_designer_specification_master as dsm')
                ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
                ->leftJoin('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
                ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
                ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
                ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
                ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
                ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
                ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
                ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'dsm.craftsman')
                ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'dsm.supplier_id')
                ->whereIn('dsm.sku', $skus)
                ->orWhereIn('dsm.sku_supplier', $skus)
                ->select([
                    'dsm.sno as spec_id',
                    'dsm.sku',
                    'dsm.sku_supplier',
                    'dsm.barcode',
                    'dsm.supplier_id as origin_supplier_id',
                    'dsm.price',
                    'dsm.sale_price',
                    'dsm.min_price',
                    'origin_supplier.name as origin_supplier_name',
                    'origin_supplier.store_url as origin_supplier_url',
                    'itemname.itemname as master_product_name',
                    'aipd.AI_product_name',
                    'itemtype.itemtype as product_type',
                    'gender.name as gender_name',
                    'composition.composition_details as composition_name',
                    'colour.colourname as colour_name',
                    'size.size as size_name',
                    'designer.designername as designer_name',
                    'craftsman.name as craftsman_name',
                ])
                ->get();

            $specIds = $specRows->pluck('spec_id')->toArray();
            $enhancedImages = DB::table('approved_enhanced_images')
                ->whereIn('specification_id', $specIds)
                ->where('status', 'approved')
                ->where('image_type', 'main')
                ->pluck('enhanced_image_path', 'specification_id');

            foreach ($specRows as $r) {
                $imgPath = $enhancedImages[$r->spec_id] ?? null;
                $data = [
                    'spec_id' => $r->spec_id,
                    'sku' => $r->sku,
                    'sku_supplier' => $r->sku_supplier,
                    'barcode' => $r->barcode,
                    'origin_supplier_id' => $r->origin_supplier_id,
                    'origin_supplier_name' => $r->origin_supplier_name ?: 'Global / In-house',
                    'origin_supplier_url' => $r->origin_supplier_url,
                    'master_product_name' => $r->master_product_name,
                    'ai_product_name' => $r->AI_product_name,
                    'product_type' => $r->product_type,
                    'gender' => $r->gender_name,
                    'composition' => $r->composition_name,
                    'colour' => $r->colour_name,
                    'size' => $r->size_name,
                    'designer' => $r->designer_name,
                    'craftsman' => $r->craftsman_name,
                    'price' => !empty($r->price) ? (float) $r->price : null,
                    'sale_price' => !empty($r->sale_price) ? (float) $r->sale_price : null,
                    'min_price' => !empty($r->min_price) ? (float) $r->min_price : null,
                    'enhanced_image' => $imgPath ? ('/' . ltrim($imgPath, '/')) : null,
                ];

                if (!empty($r->sku)) {
                    $bySku[$r->sku] = $data;
                }
                if (!empty($r->sku_supplier)) {
                    $bySku[$r->sku_supplier] = $data;
                }
            }
        }

        // Query by published WooCommerce product IDs
        if (!empty($wcProductIds)) {
            $pubRows = DB::table('published_products as pp')
                ->join('auto_designer_specification_master as dsm', 'dsm.sno', '=', 'pp.specification_id')
                ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
                ->leftJoin('AI_product_description as aipd', 'aipd.product_id', '=', 'dsm.sno')
                ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
                ->leftJoin('auto_gender_master as gender', 'gender.id', '=', 'dsm.gender')
                ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
                ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
                ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
                ->leftJoin('auto_designer_master as designer', 'designer.id', '=', 'dsm.designer_name')
                ->leftJoin('auto_craftsman_master as craftsman', 'craftsman.id', '=', 'dsm.craftsman')
                ->leftJoin('suppliers as origin_supplier', 'origin_supplier.sno', '=', 'dsm.supplier_id')
                ->leftJoin('suppliers as target_supplier', 'target_supplier.sno', '=', 'pp.target_supplier_id')
                ->whereIn('pp.woocommerce_product_id', $wcProductIds)
                ->select([
                    'pp.woocommerce_product_id',
                    'pp.target_supplier_id',
                    'target_supplier.name as target_supplier_name',
                    'target_supplier.store_url as target_supplier_url',
                    'dsm.sno as spec_id',
                    'dsm.sku',
                    'dsm.sku_supplier',
                    'dsm.barcode',
                    'dsm.supplier_id as origin_supplier_id',
                    'dsm.price',
                    'dsm.sale_price',
                    'dsm.min_price',
                    'origin_supplier.name as origin_supplier_name',
                    'origin_supplier.store_url as origin_supplier_url',
                    'itemname.itemname as master_product_name',
                    'aipd.AI_product_name',
                    'itemtype.itemtype as product_type',
                    'gender.name as gender_name',
                    'composition.composition_details as composition_name',
                    'colour.colourname as colour_name',
                    'size.size as size_name',
                    'designer.designername as designer_name',
                    'craftsman.name as craftsman_name',
                ])
                ->get();

            $specIds = $pubRows->pluck('spec_id')->toArray();
            $enhancedImages = DB::table('approved_enhanced_images')
                ->whereIn('specification_id', $specIds)
                ->where('status', 'approved')
                ->where('image_type', 'main')
                ->pluck('enhanced_image_path', 'specification_id');

            foreach ($pubRows as $r) {
                $imgPath = $enhancedImages[$r->spec_id] ?? null;
                $data = [
                    'spec_id' => $r->spec_id,
                    'sku' => $r->sku,
                    'sku_supplier' => $r->sku_supplier,
                    'barcode' => $r->barcode,
                    'origin_supplier_id' => $r->origin_supplier_id,
                    'origin_supplier_name' => $r->origin_supplier_name ?: 'Global / In-house',
                    'origin_supplier_url' => $r->origin_supplier_url,
                    'master_product_name' => $r->master_product_name,
                    'ai_product_name' => $r->AI_product_name,
                    'product_type' => $r->product_type,
                    'gender' => $r->gender_name,
                    'composition' => $r->composition_name,
                    'colour' => $r->colour_name,
                    'size' => $r->size_name,
                    'designer' => $r->designer_name,
                    'craftsman' => $r->craftsman_name,
                    'price' => !empty($r->price) ? (float) $r->price : null,
                    'sale_price' => !empty($r->sale_price) ? (float) $r->sale_price : null,
                    'min_price' => !empty($r->min_price) ? (float) $r->min_price : null,
                    'enhanced_image' => $imgPath ? ('/' . ltrim($imgPath, '/')) : null,
                ];

                $byWcId[(int) $r->woocommerce_product_id] = $data;
                if (!empty($r->sku) && !isset($bySku[$r->sku])) {
                    $bySku[$r->sku] = $data;
                }
            }
        }

        return ['by_sku' => $bySku, 'by_wc_id' => $byWcId];
    }
}
