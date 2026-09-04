<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWebhookPayload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderWebhookController extends Controller
{
    /**
     * Handle incoming WooCommerce order webhook and save raw JSON payload.
     */
    public function handle(Request $request)
    {
        try {
            $rawContent = $request->getContent();
            $payload = !empty($rawContent) ? json_decode($rawContent, true) : null;

            if (empty($payload) || !is_array($payload)) {
                $payload = $request->all();
            }

            // Extract basic identifiers if available
            $orderId = $payload['id'] ?? $payload['number'] ?? null;
            $orderNumber = $payload['number'] ?? $orderId;
            $orderKey = $payload['order_key'] ?? null;
            $status = $payload['status'] ?? null;
            $lineItems = $payload['line_items'] ?? [];

            // Save or update payload in order_webhook_payloads table (prevents duplicate rows on order.created + order.updated)
            if (!empty($orderId)) {
                $matchAttributes = ['order_id' => (string) $orderId];
                if (!empty($orderKey)) {
                    $matchAttributes['order_key'] = (string) $orderKey;
                }

                $record = OrderWebhookPayload::updateOrCreate(
                    $matchAttributes,
                    [
                        'order_id' => (string) $orderId,
                        'order_key' => $orderKey,
                        'status' => $status,
                        'payload' => $payload,
                        'headers' => $request->headers->all(),
                    ]
                );
            } else {
                $record = OrderWebhookPayload::create([
                    'order_id' => null,
                    'order_key' => $orderKey,
                    'status' => $status,
                    'payload' => $payload,
                    'headers' => $request->headers->all(),
                ]);
            }

            // Update vendor_stock rows (match item_id and barcode, set send_qty=1, avilable_qty=0)
            if (!empty($orderId)) {
                $this->processVendorStock($orderId, $orderNumber, $status, $lineItems);
            }

            Log::info("WooCommerce Order Webhook received and saved successfully.", [
                'record_id' => $record->id,
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order webhook payload processed and vendor stock updated successfully.',
                'id' => $record->id,
                'order_id' => $orderId,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error processing order webhook payload: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save order webhook payload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Deduct or manage vendor_stock when an order is received or updated.
     * Matches item_id and barcode, sets send_qty = 1 and avilable_qty = 0.
     */
    private function processVendorStock($orderId, $orderNumber, $status, array $lineItems)
    {
        try {
            $statusNormalized = strtolower(trim($status ?? ''));

            // If order is cancelled, failed, or refunded, restore vendor stock
            if (in_array($statusNormalized, ['cancelled', 'refunded', 'failed'])) {
                DB::table('vendor_stock')
                    ->where(function($q) use ($orderId, $orderNumber) {
                        $q->where('orderid', (string) $orderId)
                          ->orWhere('orderno', (string) $orderNumber);
                    })
                    ->update([
                        'send_qty' => 0,
                        'orderstatus' => $statusNormalized,
                        'updated_at' => now(),
                    ]);
                return;
            }

            // Active order statuses: processing, completed, on-hold, pending
            foreach ($lineItems as $item) {
                $sku = trim($item['sku'] ?? '');
                $wcProductId = (int) ($item['product_id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 1);

                if ($qty <= 0) continue;

                // 1. Resolve Specification
                $spec = null;
                if (!empty($sku)) {
                    $spec = DB::table('auto_designer_specification_master')
                        ->where('sku', $sku)
                        ->orWhere('sku_supplier', $sku)
                        ->orWhere('barcode', $sku)
                        ->first();
                }

                if (!$spec && !empty($wcProductId)) {
                    $published = DB::table('published_products')
                        ->where('woocommerce_product_id', $wcProductId)
                        ->first();
                    if ($published && !empty($published->specification_id)) {
                        $spec = DB::table('auto_designer_specification_master')
                            ->where('sno', $published->specification_id)
                            ->first();
                    }
                }

                if (!$spec) {
                    Log::warning("VendorStock allocation: Specification not found for SKU '{$sku}' / Product ID '{$wcProductId}' in Order #{$orderNumber}");
                    continue;
                }

                $itemId = $spec->id ?: $spec->sno;
                $barcode = $spec->barcode ?? null;

                // Check how many rows are already allocated to this order for this item
                $alreadyAllocatedCount = DB::table('vendor_stock')
                    ->where(function($q) use ($itemId, $barcode) {
                        $q->where('item_id', $itemId);
                        if (!empty($barcode)) {
                            $q->orWhere('barcode', $barcode);
                        }
                    })
                    ->where(function($q) use ($orderId, $orderNumber) {
                        $q->where('orderid', (string) $orderId)
                          ->orWhere('orderno', (string) $orderNumber);
                    })
                    ->where('send_qty', 1)
                    ->count();

                $neededQty = $qty - $alreadyAllocatedCount;
                if ($neededQty <= 0) {
                    // Update orderstatus on already allocated rows if changed
                    DB::table('vendor_stock')
                        ->where(function($q) use ($itemId, $barcode) {
                            $q->where('item_id', $itemId);
                            if (!empty($barcode)) {
                                $q->orWhere('barcode', $barcode);
                            }
                        })
                        ->where(function($q) use ($orderId, $orderNumber) {
                            $q->where('orderid', (string) $orderId)
                              ->orWhere('orderno', (string) $orderNumber);
                        })
                        ->update([
                            'orderstatus' => $statusNormalized ?: 'processing',
                            'updated_at' => now(),
                        ]);
                    continue;
                }

                // Find $neededQty available rows matching item_id and barcode where send_qty == 0
                $availableRows = DB::table('vendor_stock')
                    ->where(function($q) use ($itemId, $barcode) {
                        $q->where('item_id', $itemId);
                        if (!empty($barcode)) {
                            $q->orWhere('barcode', $barcode);
                        }
                    })
                    ->where(function($q) {
                        $q->where('send_qty', 0)
                          ->orWhereNull('send_qty');
                    })
                    ->where(function($q) {
                        $q->where('avilable_qty', '>', 0)
                          ->orWhereNull('avilable_qty');
                    })
                    ->orderBy('sno', 'asc')
                    ->limit($neededQty)
                    ->pluck('sno');

                if ($availableRows->isNotEmpty()) {
                    DB::table('vendor_stock')
                        ->whereIn('sno', $availableRows)
                        ->update([
                            'send_qty' => 1,
                            'orderid' => (string) $orderId,
                            'orderno' => (string) $orderNumber,
                            'orderstatus' => $statusNormalized ?: 'processing',
                            'updated_at' => now(),
                        ]);

                    Log::info("VendorStock allocated: {$availableRows->count()} units for item_id {$itemId} (SKU: {$spec->sku}, Barcode: {$barcode}) in Order #{$orderNumber}");
                } else {
                    Log::warning("VendorStock: No available stock units found for item_id {$itemId} (SKU: {$spec->sku}, Barcode: {$barcode}) in Order #{$orderNumber}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error in processVendorStock for Order #{$orderNumber}: " . $e->getMessage());
        }
    }
}
