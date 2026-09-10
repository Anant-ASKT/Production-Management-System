<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWebhookPayload;
use App\Models\OrderHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupplierOrderDetailsMail;

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
            $incomingStatus = strtolower(trim($payload['status'] ?? ''));
            $lineItems = $payload['line_items'] ?? [];

            // Check if record already exists
            $existingRecord = null;
            if (!empty($orderId)) {
                $existingRecord = OrderWebhookPayload::where('order_id', (string) $orderId)->first();
            }

            // Determine status: If already marked as Shipped or Delivered in PMS, keep it; otherwise set to "Order confirmed"
            if ($existingRecord && in_array(strtolower(trim($existingRecord->status ?? '')), ['shipped', 'delivered'])) {
                $status = $existingRecord->status;
            } elseif (in_array($incomingStatus, ['cancelled', 'refunded', 'failed'])) {
                $status = ucfirst($incomingStatus);
            } else {
                $status = 'Order confirmed';
            }

            $isNew = !$existingRecord;

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

            // Record initial history if newly created
            if ($isNew && $record) {
                OrderHistory::create([
                    'order_webhook_payload_id' => $record->id,
                    'user_type' => 'system',
                    'user_id' => null,
                    'user_name' => 'Website Webhook',
                    'action' => 'Order Confirmed',
                    'from_status' => null,
                    'to_status' => $status,
                    'comment' => 'Order received from website and confirmed automatically.',
                ]);

                // Automatically send new order email to supplier company email
                $this->sendAutomatedSupplierOrderEmail($record, $payload, $request->headers->all());
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

    /**
     * Send automated new order notification email to the supplier company email.
     */
    private function sendAutomatedSupplierOrderEmail($record, array $payload, array $headers)
    {
        try {
            $allSuppliers = DB::table('suppliers')->get();
            $sourceUrl = $headers['x-wc-webhook-source'][0] ?? ($headers['host'][0] ?? '');
            $cleanSource = preg_replace('#^https?://#i', '', rtrim(trim($sourceUrl), '/'));

            $sellingSupplier = null;
            if (!empty($cleanSource)) {
                foreach ($allSuppliers as $sup) {
                    $cleanSupUrl = preg_replace('#^https?://#i', '', rtrim(trim($sup->store_url ?? ''), '/'));
                    if (!empty($cleanSupUrl) && (str_contains($cleanSource, $cleanSupUrl) || str_contains($cleanSupUrl, $cleanSource))) {
                        $sellingSupplier = $sup;
                        break;
                    }
                }
            }

            // Identify recipient supplier company email(s)
            $recipientEmails = [];
            if ($sellingSupplier && !empty($sellingSupplier->email)) {
                $recipientEmails[] = trim($sellingSupplier->email);
            }

            // Also check origin suppliers for line items
            $lineItems = $payload['line_items'] ?? [];
            $skus = [];
            foreach ($lineItems as $item) {
                if (!empty($item['sku'])) {
                    $skus[] = trim($item['sku']);
                }
            }

            if (!empty($skus)) {
                $originSupplierIds = DB::table('auto_designer_specification_master')
                    ->whereIn('sku', $skus)
                    ->orWhereIn('sku_supplier', $skus)
                    ->pluck('supplier_id')
                    ->filter()
                    ->unique();

                foreach ($originSupplierIds as $sId) {
                    $originSup = $allSuppliers->firstWhere('sno', $sId);
                    if ($originSup && !empty($originSup->email)) {
                        $recipientEmails[] = trim($originSup->email);
                    }
                }
            }

            $recipientEmails = array_unique(array_filter($recipientEmails));

            if (empty($recipientEmails)) {
                Log::info("No supplier company email found for auto order email notification.", [
                    'order_id' => $record->order_id,
                    'record_id' => $record->id,
                ]);
                return;
            }

            // Enrich line items with SKU / Spec details if available
            $enrichedLineItems = [];
            $specRows = !empty($skus) ? DB::table('auto_designer_specification_master')
                ->whereIn('sku', $skus)
                ->orWhereIn('sku_supplier', $skus)
                ->get()
                ->keyBy('sku') : collect();

            foreach ($lineItems as $item) {
                $sku = trim($item['sku'] ?? '');
                $spec = $specRows[$sku] ?? null;
                $enrichedLineItems[] = array_merge($item, [
                    'resolved_sku' => !empty($sku) ? $sku : ($spec->sku ?? '—'),
                    'spec_id' => $spec->sno ?? null,
                    'colour' => $spec->colour ?? null,
                    'size' => $spec->sizes ?? null,
                ]);
            }

            // Prepare order data array for SupplierOrderDetailsMail
            $orderNumber = $payload['number'] ?? $record->order_id ?? ('#' . $record->id);
            $storeName = $sellingSupplier ? $sellingSupplier->name : (!empty($cleanSource) ? $cleanSource : 'Website Store');

            $orderData = [
                'record_id' => $record->id,
                'order_id' => $record->order_id ?: $orderNumber,
                'order_number' => $orderNumber,
                'status' => $record->status ?? 'Order confirmed',
                'date_created' => !empty($payload['date_created']) ? date('d M Y, h:i A', strtotime($payload['date_created'])) : date('d M Y, h:i A'),
                'currency_symbol' => $payload['currency_symbol'] ?? '₹',
                'currency' => $payload['currency'] ?? 'INR',
                'total' => isset($payload['total']) ? (float) $payload['total'] : 0,
                'subtotal' => isset($payload['subtotal']) ? (float) $payload['subtotal'] : (isset($payload['total']) ? (float) $payload['total'] : 0),
                'discount_total' => isset($payload['discount_total']) ? (float) $payload['discount_total'] : 0,
                'shipping_total' => isset($payload['shipping_total']) ? (float) $payload['shipping_total'] : 0,
                'total_tax' => isset($payload['total_tax']) ? (float) $payload['total_tax'] : 0,
                'payment_method' => $payload['payment_method_title'] ?? ($payload['payment_method'] ?? 'N/A'),
                'customer_note' => $payload['customer_note'] ?? '',
                'billing' => $payload['billing'] ?? [],
                'shipping' => $payload['shipping'] ?? [],
                'line_items' => $enrichedLineItems,
                'shipping_lines' => $payload['shipping_lines'] ?? [],
                'selling_supplier_name' => $storeName,
                'selling_supplier_url' => $sellingSupplier ? ($sellingSupplier->store_url ?? null) : $sourceUrl,
            ];

            $subject = "New Order #{$orderNumber} Received - {$storeName}";
            $customMessage = "A new customer order #{$orderNumber} has been received from your website and confirmed automatically.";

            foreach ($recipientEmails as $email) {
                Mail::to($email)->send(new SupplierOrderDetailsMail($orderData, $subject, $customMessage));

                OrderHistory::create([
                    'order_webhook_payload_id' => $record->id,
                    'user_type' => 'system',
                    'user_id' => null,
                    'user_name' => 'System Auto-Mail',
                    'action' => 'Email Sent to Supplier',
                    'from_status' => null,
                    'to_status' => $record->status,
                    'comment' => "Automated new order notification email sent to supplier company email: {$email}",
                ]);

                Log::info("Automated order email sent to supplier company email.", [
                    'recipient' => $email,
                    'order_id' => $orderNumber,
                    'record_id' => $record->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to auto-send supplier order email: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}
