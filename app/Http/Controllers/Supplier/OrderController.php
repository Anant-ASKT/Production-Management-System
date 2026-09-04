<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderWebhookPayload;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display the supplier's website orders page.
     */
    public function index()
    {
        $user = auth()->guard('supplier')->user();
        if (!$user) {
            return redirect()->route('supplier.login');
        }

        if (!$this->authorizeOwner($user)) {
            abort(403, 'Unauthorized. Website orders are only accessible to the supplier owner.');
        }

        $supplierId = $user->supplier_id;
        $supplierContext = $this->getSupplierProductContext($supplierId);

        // Compute summary metrics for this supplier
        $allOrders = OrderWebhookPayload::whereNotNull('order_id')->orderByDesc('id')->get();
        $supplierOrders = $allOrders->filter(function ($record) use ($supplierContext) {
            return $this->orderContainsSupplierItems($record, $supplierContext);
        });

        $totalOrders = $supplierOrders->count();
        $processingOrders = $supplierOrders->where('status', 'processing')->count();
        $completedOrders = $supplierOrders->where('status', 'completed')->count();
        $pendingOrders = $supplierOrders->filter(fn($o) => in_array($o->status, ['pending', 'on-hold']))->count();

        return view('supplier.orders.index', compact(
            'totalOrders',
            'processingOrders',
            'completedOrders',
            'pendingOrders'
        ));
    }

    /**
     * Fetch paginated and filtered JSON data of orders containing this supplier's products.
     */
    public function data(Request $request)
    {
        $user = auth()->guard('supplier')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if (!$this->authorizeOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Website orders are only accessible to the supplier owner.'
            ], 403);
        }

        $supplierId = $user->supplier_id;
        $supplierContext = $this->getSupplierProductContext($supplierId);

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 30, 50, 100], true)) {
            $perPage = 20;
        }

        $search = strtolower(trim($request->input('search', '')));
        $status = strtolower(trim($request->input('status', '')));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $allSuppliers = DB::table('suppliers')->get();

        $query = OrderWebhookPayload::whereNotNull('order_id');

        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $records = $query->orderByDesc('id')->get();

        // Filter only orders that contain this supplier's products
        $filtered = $records->filter(function ($record) use ($supplierContext, $search) {
            $hasSupplierItem = $this->orderContainsSupplierItems($record, $supplierContext);
            if (!$hasSupplierItem) return false;

            if (!empty($search)) {
                $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
                $billing = $payload['billing'] ?? [];
                $shipping = $payload['shipping'] ?? [];
                $custName = strtolower(trim(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')));
                $orderNum = strtolower((string) ($payload['number'] ?? $record->order_id ?? ''));
                $city = strtolower((string) ($shipping['city'] ?? $billing['city'] ?? ''));

                $itemMatch = false;
                foreach ($payload['line_items'] ?? [] as $it) {
                    $sku = strtolower((string) ($it['sku'] ?? ''));
                    $name = strtolower((string) ($it['name'] ?? ''));
                    if (str_contains($sku, $search) || str_contains($name, $search)) {
                        $itemMatch = true;
                        break;
                    }
                }

                return str_contains($orderNum, $search)
                    || str_contains($custName, $search)
                    || str_contains($city, $search)
                    || $itemMatch;
            }

            return true;
        });

        // Pagination
        $currentPage = (int) $request->input('page', 1);
        $total = $filtered->count();
        $lastPage = (int) ceil($total / $perPage) ?: 1;
        $itemsSlice = $filtered->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $transformed = $itemsSlice->map(function ($record) use ($supplierContext, $allSuppliers) {
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
                $customerName = 'Customer';
            }

            $customerCity = $shipping['city'] ?? ($billing['city'] ?? '—');
            $customerState = $shipping['state'] ?? ($billing['state'] ?? '');
            $customerCountry = $shipping['country'] ?? ($billing['country'] ?? '');
            $currencySymbol = $payload['currency_symbol'] ?? '₹';

            $storeInfo = $this->resolveSellingSupplier($headers, $allSuppliers);

            // Filter supplier specific items and calculate supplier total
            $supplierItems = [];
            $supplierTotal = 0;

            foreach ($lineItems as $item) {
                $sku = trim($item['sku'] ?? '');
                $wcPid = (int) ($item['product_id'] ?? 0);

                $isMatch = in_array($sku, $supplierContext['skus'], true)
                    || in_array($wcPid, $supplierContext['wc_product_ids'], true);

                $specData = $supplierContext['specs_by_sku'][$sku] ?? ($supplierContext['specs_by_wc_id'][$wcPid] ?? null);

                if ($isMatch || $specData) {
                    $qty = (int) ($item['quantity'] ?? 1);
                    $price = isset($item['price']) ? (float) $item['price'] : 0;
                    $lineTotal = isset($item['total']) ? (float) $item['total'] : ($price * $qty);
                    $supplierTotal += $lineTotal;

                    $imgSrc = $specData['enhanced_image'] ?? ($item['image']['src'] ?? null);

                    $supplierItems[] = [
                        'id' => $item['id'] ?? null,
                        'name' => $item['name'] ?? 'Product',
                        'quantity' => $qty,
                        'price' => $price,
                        'total' => $lineTotal,
                        'sku' => !empty($sku) ? $sku : ($specData['sku'] ?? '—'),
                        'image' => $imgSrc,
                        'colour' => $specData['colour'] ?? null,
                        'size' => $specData['size'] ?? null,
                        'product_type' => $specData['product_type'] ?? null,
                    ];
                }
            }

            return [
                'id' => $record->id,
                'order_id' => $record->order_id ?: ($payload['number'] ?? ('#' . $record->id)),
                'order_number' => $payload['number'] ?? $record->order_id ?? ('#' . $record->id),
                'order_key' => $record->order_key ?: ($payload['order_key'] ?? '—'),
                'status' => strtolower($record->status ?: ($payload['status'] ?? 'pending')),
                'courier_name' => $record->courier_name,
                'tracking_id' => $record->tracking_id,
                'tracking_url' => $record->tracking_url,
                'shipped_at_formatted' => $record->shipped_at ? $record->shipped_at->format('d M Y, h:i A') : null,
                'order_date' => !empty($payload['date_created']) ? date('d M Y, h:i A', strtotime($payload['date_created'])) : ($record->created_at ? $record->created_at->format('d M Y, h:i A') : '—'),
                'customer_name' => $customerName,
                'customer_location' => trim("{$customerCity} {$customerState} {$customerCountry}"),
                'currency_symbol' => $currencySymbol,
                'supplier_total' => $supplierTotal,
                'supplier_total_formatted' => $currencySymbol . number_format($supplierTotal, 2),
                'order_total' => isset($payload['total']) ? (float) $payload['total'] : $supplierTotal,
                'order_total_formatted' => $currencySymbol . number_format(isset($payload['total']) ? (float) $payload['total'] : $supplierTotal, 2),
                'payment_method' => $payload['payment_method_title'] ?? ($payload['payment_method'] ?? 'N/A'),
                'items_count' => count($supplierItems),
                'supplier_items' => $supplierItems,
                'selling_store_name' => $storeInfo['name'],
                'selling_store_url' => $storeInfo['store_url'],
                'source_store' => $storeInfo['domain'],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformed,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
            'from' => $total > 0 ? (($currentPage - 1) * $perPage + 1) : 0,
            'to' => min($currentPage * $perPage, $total)
        ]);
    }

    /**
     * Show dedicated simple order details page for the supplier.
     */
    public function show(Request $request, $id)
    {
        $user = auth()->guard('supplier')->user();
        if (!$user) {
            return redirect()->route('supplier.login');
        }

        if (!$this->authorizeOwner($user)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Website orders are only accessible to the supplier owner.'
                ], 403);
            }
            abort(403, 'Unauthorized. Website orders are only accessible to the supplier owner.');
        }

        $supplierId = $user->supplier_id;
        $supplierContext = $this->getSupplierProductContext($supplierId);

        $record = OrderWebhookPayload::with('histories')->findOrFail($id);

        // Security Check: Verify that this order contains products belonging to this supplier
        if (!$this->orderContainsSupplierItems($record, $supplierContext)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or you do not have permission to view this order.'
                ], 404);
            }
            abort(404, 'Order not found or you do not have permission to view this order.');
        }

        $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
        $headers = is_array($record->headers) ? $record->headers : (json_decode($record->headers, true) ?? []);

        $allSuppliers = DB::table('suppliers')->get();
        $storeInfo = $this->resolveSellingSupplier($headers, $allSuppliers);

        $lineItems = $payload['line_items'] ?? [];
        $supplierItems = [];
        $supplierSubtotal = 0;

        foreach ($lineItems as $item) {
            $sku = trim($item['sku'] ?? '');
            $wcPid = (int) ($item['product_id'] ?? 0);

            $isMatch = in_array($sku, $supplierContext['skus'], true)
                || in_array($wcPid, $supplierContext['wc_product_ids'], true);

            $specData = $supplierContext['specs_by_sku'][$sku] ?? ($supplierContext['specs_by_wc_id'][$wcPid] ?? null);

            if ($isMatch || $specData) {
                $price = isset($item['price']) ? (float) $item['price'] : 0;
                $qty = (int) ($item['quantity'] ?? 1);
                $lineTotal = isset($item['total']) ? (float) $item['total'] : ($price * $qty);
                $supplierSubtotal += $lineTotal;

                $imgSrc = $specData['enhanced_image'] ?? ($item['image']['src'] ?? null);

                $supplierItems[] = [
                    'id' => $item['id'] ?? null,
                    'name' => $item['name'] ?? 'Product',
                    'quantity' => $qty,
                    'price' => $price,
                    'total' => $lineTotal,
                    'sku' => !empty($sku) ? $sku : ($specData['sku'] ?? '—'),
                    'barcode' => $specData['barcode'] ?? '—',
                    'image' => $imgSrc,
                    'colour' => $specData['colour'] ?? null,
                    'size' => $specData['size'] ?? null,
                    'composition' => $specData['composition'] ?? null,
                    'product_type' => $specData['product_type'] ?? null,
                    'spec_price' => $specData['price'] ?? null,
                    'spec_sale_price' => $specData['sale_price'] ?? null,
                ];
            }
        }

        $orderData = [
            'record_id' => $record->id,
            'order_number' => $payload['number'] ?? $record->order_id ?? ('#' . $record->id),
            'order_key' => $record->order_key ?: ($payload['order_key'] ?? '—'),
            'status' => strtolower($record->status ?: ($payload['status'] ?? 'pending')),
            'courier_name' => $record->courier_name,
            'tracking_id' => $record->tracking_id,
            'tracking_url' => $record->tracking_url,
            'shipped_at' => $record->shipped_at ? $record->shipped_at->format('Y-m-d\TH:i') : null,
            'shipped_at_formatted' => $record->shipped_at ? $record->shipped_at->format('d M Y, h:i A') : null,
            'shipping_notes' => $record->shipping_notes,
            'histories' => $record->histories,
            'date_created' => !empty($payload['date_created']) ? date('d M Y, h:i A', strtotime($payload['date_created'])) : ($record->created_at ? $record->created_at->format('d M Y, h:i A') : '—'),
            'currency_symbol' => $payload['currency_symbol'] ?? '₹',
            'supplier_total' => $supplierSubtotal,
            'supplier_total_formatted' => ($payload['currency_symbol'] ?? '₹') . number_format($supplierSubtotal, 2),
            'billing' => $payload['billing'] ?? [],
            'shipping' => $payload['shipping'] ?? [],
            'supplier_items' => $supplierItems,
            'selling_store_name' => $storeInfo['name'],
            'selling_store_url' => $storeInfo['store_url'],
            'source_store' => $storeInfo['domain'],
            'customer_note' => $payload['customer_note'] ?? '',
            'payment_method' => $payload['payment_method_title'] ?? ($payload['payment_method'] ?? 'N/A'),
        ];

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $orderData
            ]);
        }

        return view('supplier.orders.show', compact('orderData'));
    }

    /**
     * Update order status, tracking ID, and shipping details with full audit trail history.
     */
    public function updateStatusAndShipping(Request $request, $id)
    {
        $user = auth()->guard('supplier')->user();
        if (!$user) {
            return redirect()->route('supplier.login');
        }

        if (!$this->authorizeOwner($user)) {
            abort(403, 'Unauthorized. Website orders are only accessible to the supplier owner.');
        }

        $supplierId = $user->supplier_id;
        $supplierContext = $this->getSupplierProductContext($supplierId);

        $record = OrderWebhookPayload::findOrFail($id);

        // Security check: must contain this supplier's products
        if (!$this->orderContainsSupplierItems($record, $supplierContext)) {
            abort(404, 'Order not found or you do not have permission to modify this order.');
        }

        $request->validate([
            'status' => 'required|string|in:pending,processing,on-hold,shipped,completed,cancelled,refunded,failed',
            'courier_name' => 'nullable|string|max:100',
            'tracking_id' => 'nullable|string|max:100',
            'tracking_url' => 'nullable|string|max:500',
            'shipped_at' => 'nullable|date',
            'shipping_notes' => 'nullable|string|max:1000',
            'comment' => 'nullable|string|max:1000',
        ]);

        $oldStatus = strtolower(trim($record->status ?: 'pending'));
        $newStatus = strtolower(trim($request->status));
        $statusChanged = ($oldStatus !== $newStatus);

        $courierName = trim($request->courier_name ?? '');
        $trackingId = trim($request->tracking_id ?? '');
        $trackingUrl = trim($request->tracking_url ?? '');

        if (empty($trackingUrl) && !empty($courierName) && !empty($trackingId)) {
            $trackingUrl = $this->generateTrackingUrl($courierName, $trackingId) ?? '';
        }

        $shippedAt = $request->filled('shipped_at') ? Carbon::parse($request->shipped_at) : $record->shipped_at;
        if (in_array($newStatus, ['shipped', 'completed'], true) && empty($shippedAt)) {
            $shippedAt = now();
        }

        $shippingChanged = ($record->courier_name !== ($courierName ?: null))
            || ($record->tracking_id !== ($trackingId ?: null))
            || ($record->tracking_url !== ($trackingUrl ?: null))
            || ($record->shipping_notes !== ($request->shipping_notes ?: null));

        // Update database record
        $record->status = $newStatus;
        $record->courier_name = $courierName ?: null;
        $record->tracking_id = $trackingId ?: null;
        $record->tracking_url = $trackingUrl ?: null;
        $record->shipped_at = $shippedAt;
        $record->shipping_notes = $request->shipping_notes ?: null;

        $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
        $payload['status'] = $newStatus;
        $record->payload = $payload;
        $record->save();

        // Determine action name for audit log
        $action = 'Order Updated';
        if ($statusChanged && $shippingChanged) {
            $action = 'Status & Shipping Updated';
        } elseif ($statusChanged) {
            $action = 'Status Changed to ' . ucfirst($newStatus);
        } elseif ($shippingChanged) {
            $action = !empty($trackingId) ? 'Tracking Added' : 'Shipping Details Updated';
        } elseif ($request->filled('comment')) {
            $action = 'Note Added';
        }

        // Build structured comment
        $commentParts = [];
        if ($statusChanged) {
            $commentParts[] = "Status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus);
        }
        if (!empty($courierName) && !empty($trackingId)) {
            $commentParts[] = "Dispatched via {$courierName} (AWB / Tracking: {$trackingId})";
        } elseif (!empty($trackingId)) {
            $commentParts[] = "Tracking AWB: {$trackingId}";
        }
        if ($request->filled('shipping_notes')) {
            $commentParts[] = "Shipping Note: " . trim($request->shipping_notes);
        }
        if ($request->filled('comment')) {
            $commentParts[] = "Remark: " . trim($request->comment);
        }

        OrderHistory::create([
            'order_webhook_payload_id' => $record->id,
            'user_type' => 'supplier',
            'user_id' => $user->sno,
            'user_name' => $user->name . ' (' . ($user->role ?? 'Owner') . ')',
            'action' => $action,
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'courier_name' => $courierName ?: null,
            'tracking_id' => $trackingId ?: null,
            'comment' => !empty($commentParts) ? implode(' | ', $commentParts) : 'Order details updated',
        ]);

        return redirect()->route('supplier.orders.show', $record->id)->with('success', 'Order status and shipping details updated successfully.');
    }

    /**
     * Auto-generate tracking URL for popular Indian and International couriers.
     */
    private function generateTrackingUrl(?string $courier, ?string $trackingId): ?string
    {
        if (empty($courier) || empty($trackingId)) {
            return null;
        }

        $c = strtolower(trim($courier));
        $t = trim($trackingId);

        if (str_contains($c, 'delhivery')) {
            return "https://www.delhivery.com/track/package/{$t}";
        }
        if (str_contains($c, 'bluedart') || str_contains($c, 'blue dart')) {
            return "https://www.bluedart.com/tracking?track={$t}";
        }
        if (str_contains($c, 'dtdc')) {
            return "https://www.dtdc.in/tracking/shipment-tracking.asp?trackingNo={$t}";
        }
        if (str_contains($c, 'india post') || str_contains($c, 'speed post') || str_contains($c, 'postal')) {
            return "https://www.indiapost.gov.in/_layouts/15/dpt.cptc.va/trackconsignment.aspx";
        }
        if (str_contains($c, 'fedex')) {
            return "https://www.fedex.com/fedextrack/?trknbr={$t}";
        }
        if (str_contains($c, 'dhl')) {
            return "https://www.dhl.com/en/express/tracking.html?AWB={$t}";
        }
        if (str_contains($c, 'ekart')) {
            return "https://ekartlogistics.com/shipmenttrack/{$t}";
        }
        if (str_contains($c, 'shadowfax')) {
            return "https://tracker.shadowfax.in/#/track/{$t}";
        }
        if (str_contains($c, 'xpressbees') || str_contains($c, 'xpress bees')) {
            return "https://www.xpressbees.com/track?isawb=Yes&trackid={$t}";
        }
        if (str_contains($c, 'ecom express') || str_contains($c, 'ecom')) {
            return "https://ecomexpress.in/tracking/?awb_number={$t}";
        }
        if (str_contains($c, 'shiprocket')) {
            return "https://shiprocket.co/tracking/{$t}";
        }

        return null;
    }

    /**
     * Get all identifiers for products belonging to the given supplier.
     */
    private function getSupplierProductContext($supplierId)
    {
        // 1. Fetch from auto_designer_specification_master
        $specs = DB::table('auto_designer_specification_master as dsm')
            ->leftJoin('auto_itemname_master as itemname', 'itemname.id', '=', 'dsm.item_name')
            ->leftJoin('auto_itemtype_master as itemtype', 'itemtype.id', '=', 'dsm.item_type')
            ->leftJoin('auto_colour_master as colour', 'colour.id', '=', 'dsm.colour')
            ->leftJoin('auto_size_master as size', 'size.id', '=', 'dsm.sizes')
            ->leftJoin('auto_composition_master_stock as composition', 'composition.id', '=', 'dsm.composition')
            ->where('dsm.supplier_id', $supplierId)
            ->orWhere('dsm.supplier_person_id', $supplierId)
            ->select([
                'dsm.sno as spec_id',
                'dsm.sku',
                'dsm.sku_supplier',
                'dsm.barcode',
                'dsm.price',
                'dsm.sale_price',
                'itemname.itemname as master_product_name',
                'itemtype.itemtype as product_type',
                'colour.colourname as colour',
                'size.size as size',
                'composition.composition_details as composition',
            ])
            ->get();

        $specIds = $specs->pluck('spec_id')->toArray();
        $skus = [];
        $specsBySku = [];

        $enhancedImages = DB::table('approved_enhanced_images')
            ->whereIn('specification_id', $specIds)
            ->where('status', 'approved')
            ->where('image_type', 'main')
            ->pluck('enhanced_image_path', 'specification_id');

        foreach ($specs as $s) {
            $imgPath = $enhancedImages[$s->spec_id] ?? null;
            $data = [
                'spec_id' => $s->spec_id,
                'sku' => $s->sku,
                'barcode' => $s->barcode,
                'price' => $s->price,
                'sale_price' => $s->sale_price,
                'product_type' => $s->product_type,
                'colour' => $s->colour,
                'size' => $s->size,
                'composition' => $s->composition,
                'enhanced_image' => $imgPath ? ('/' . ltrim($imgPath, '/')) : null,
            ];

            if (!empty($s->sku)) {
                $skus[] = $s->sku;
                $specsBySku[$s->sku] = $data;
            }
            if (!empty($s->sku_supplier)) {
                $skus[] = $s->sku_supplier;
                $specsBySku[$s->sku_supplier] = $data;
            }
            if (!empty($s->barcode)) {
                $specsBySku[$s->barcode] = $data;
            }
        }

        // 2. Fetch WooCommerce product IDs published for this origin supplier
        $published = DB::table('published_products')
            ->where('origin_supplier_id', $supplierId)
            ->orWhere('target_supplier_id', $supplierId)
            ->get();

        $wcProductIds = [];
        $specsByWcId = [];
        foreach ($published as $pub) {
            if (!empty($pub->woocommerce_product_id)) {
                $wcPid = (int) $pub->woocommerce_product_id;
                $wcProductIds[] = $wcPid;
                if (!empty($pub->specification_id)) {
                    $matchedSpec = $specs->firstWhere('spec_id', $pub->specification_id);
                    if ($matchedSpec) {
                        $specsByWcId[$wcPid] = $specsBySku[$matchedSpec->sku] ?? [
                            'spec_id' => $matchedSpec->spec_id,
                            'sku' => $matchedSpec->sku,
                            'barcode' => $matchedSpec->barcode,
                        ];
                    }
                }
            }
        }

        return [
            'skus' => array_unique(array_filter($skus)),
            'wc_product_ids' => array_unique(array_filter($wcProductIds)),
            'specs_by_sku' => $specsBySku,
            'specs_by_wc_id' => $specsByWcId,
        ];
    }

    /**
     * Check if a given order webhook record contains any items belonging to this supplier.
     */
    private function orderContainsSupplierItems($record, $supplierContext)
    {
        $payload = is_array($record->payload) ? $record->payload : (json_decode($record->payload, true) ?? []);
        $lineItems = $payload['line_items'] ?? [];

        foreach ($lineItems as $item) {
            $sku = trim($item['sku'] ?? '');
            $wcPid = (int) ($item['product_id'] ?? 0);

            if (in_array($sku, $supplierContext['skus'], true)) {
                return true;
            }
            if (in_array($wcPid, $supplierContext['wc_product_ids'], true)) {
                return true;
            }
            if (isset($supplierContext['specs_by_sku'][$sku]) || isset($supplierContext['specs_by_wc_id'][$wcPid])) {
                return true;
            }
        }

        return false;
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
                        'name' => $sup->name,
                        'store_url' => $sup->store_url,
                        'domain' => $cleanSupUrl
                    ];
                }
            }
        }

        return [
            'name' => !empty($cleanSource) ? $cleanSource : 'Website Store',
            'store_url' => $sourceUrl,
            'domain' => !empty($cleanSource) ? $cleanSource : 'Website Store'
        ];
    }

    /**
     * Check if the authenticated supplier user is an Owner.
     */
    private function authorizeOwner($user): bool
    {
        return $user && strcasecmp(trim($user->role ?? 'Owner'), 'Owner') === 0;
    }
}
