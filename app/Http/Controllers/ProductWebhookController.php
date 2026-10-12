<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PublishedProduct;
use App\Models\PublishedProductUpdateLog;

class ProductWebhookController extends Controller
{
    /**
     * Handle incoming WooCommerce Product Webhooks (deleted, restored, updated, created).
     */
    public function handle(Request $request)
    {
        try {
            $rawContent = $request->getContent();
            $payload = !empty($rawContent) ? json_decode($rawContent, true) : null;

            if (empty($payload) || !is_array($payload)) {
                $payload = $request->all();
            }

            $webhookId = $payload['webhook_id'] ?? $request->input('webhook_id') ?? null;
            $wcProductId = $payload['id'] ?? $request->input('id') ?? null;
            $sku = trim($payload['sku'] ?? $request->input('sku') ?? '');
            $status = strtolower(trim($payload['status'] ?? $request->input('status') ?? ''));

            // 1. Check for WooCommerce Webhook Ping/Test Payload
            if (!empty($webhookId) && empty($wcProductId)) {
                Log::info('WooCommerce Product Webhook ping received.', ['webhook_id' => $webhookId, 'payload' => $payload]);
                return response()->json([
                    'success' => true,
                    'message' => 'WooCommerce Product Webhook ping received successfully.',
                    'webhook_id' => $webhookId
                ], 200);
            }


            // 2. Identify Event / Topic from WooCommerce Headers or Payload
            $topic = strtolower(trim(
                $request->header('x-wc-webhook-topic') ??
                $request->header('X-WC-Webhook-Topic') ??
                ($payload['topic'] ?? '')
            ));

            $event = strtolower(trim(
                $request->header('x-wc-webhook-event') ??
                $request->header('X-WC-Webhook-Event') ??
                ($payload['event'] ?? '')
            ));

            $sourceUrl = $request->header('x-wc-webhook-source') ??
                $request->header('X-WC-Webhook-Source') ??
                $request->header('host') ?? '';

            // 3. Extract Product Details
            $wcProductId = $payload['id'] ?? null;
            $sku = trim($payload['sku'] ?? '');
            $status = strtolower(trim($payload['status'] ?? ''));

            Log::info("WooCommerce Product Webhook incoming: topic='{$topic}', event='{$event}', wc_id='{$wcProductId}', status='{$status}'", [
                'headers' => $request->headers->all(),
                'wc_id' => $wcProductId,
                'sku' => $sku,
                'status' => $status
            ]);

            if (empty($wcProductId) && empty($sku)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing product ID or SKU in webhook payload.'
                ], 400);
            }

            // Determine action type
            $isDeleted = ($event === 'deleted') ||
                str_contains($topic, 'deleted') ||
                str_contains($topic, 'trash') ||
                $status === 'trash';

            $isRestored = ($event === 'restored') ||
                str_contains($topic, 'restored');

            if ($isDeleted) {
                return $this->processProductDeleted($wcProductId, $sku, $payload, $sourceUrl);
            }

            if ($isRestored) {
                return $this->processProductRestored($wcProductId, $sku, $payload, $sourceUrl);
            }

            // General update (e.g. stock or status changed on WooCommerce)
            return $this->processProductUpdated($wcProductId, $sku, $payload, $sourceUrl);

        } catch (\Throwable $e) {
            Log::error('Error processing WooCommerce Product Webhook: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process product webhook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dedicated action for product deleted webhook route.
     */
    public function handleDeleted(Request $request)
    {
        return $this->handle($request);
    }

    /**
     * Process product deletion / trashing from WooCommerce store.
     */
    protected function processProductDeleted($wcProductId, $sku, array $payload, string $sourceUrl)
    {
        $publishedRecord = null;

        if (!empty($wcProductId)) {
            $publishedRecord = DB::table('published_products')
                ->where('woocommerce_product_id', $wcProductId)
                ->first();
        }

        // Fallback: match by SKU if not found by wcProductId
        if (!$publishedRecord && !empty($sku)) {
            $spec = DB::table('auto_designer_specification_master')
                ->where('sku', $sku)
                ->orWhere('sku_supplier', $sku)
                ->first();

            if ($spec) {
                $publishedRecord = DB::table('published_products')
                    ->where('specification_id', $spec->sno)
                    ->orderByDesc('updated_at')
                    ->first();
            }
        }

        if (!$publishedRecord) {
            Log::info("WooCommerce Product Webhook [delete]: No published record found for WC ID #{$wcProductId} or SKU '{$sku}'.");
            return response()->json([
                'success' => true,
                'message' => "Product #{$wcProductId} deleted on WooCommerce. No active matching publication found in PMS database.",
                'woocommerce_product_id' => $wcProductId
            ], 200);
        }

        $specId = $publishedRecord->specification_id;
        $spec = DB::table('auto_designer_specification_master')->where('sno', $specId)->first();

        // 1. Current ERP Stock count
        $currentStock = 0;
        if ($spec) {
            $currentStock = DB::table('vendor_stock')
                ->where(function ($q) use ($spec) {
                    $q->where('item_id', $spec->sno);
                    if (!empty($spec->barcode)) {
                        $q->orWhere('barcode', $spec->barcode);
                    }
                })
                ->where(function ($q) {
                    $q->where('send_qty', 0)->orWhereNull('send_qty');
                })
                ->where(function ($q) {
                    $q->where('avilable_qty', '>', 0)->orWhereNull('avilable_qty');
                })
                ->count();
        }

        DB::beginTransaction();
        try {
            // 2. Update published_products table -> mark status as 'trashed'
            DB::table('published_products')
                ->where('sno', $publishedRecord->sno)
                ->update([
                    'status' => 'trashed',
                    'updated_at' => now(),
                ]);

            // 3. Update auto_designer_specification_master
            // Check if any other store still has this spec active as published
            $remainingActive = DB::table('published_products')
                ->where('specification_id', $specId)
                ->where('status', 'published')
                ->count();

            if ($remainingActive === 0) {
                DB::table('auto_designer_specification_master')
                    ->where('sno', $specId)
                    ->update([
                        'status' => 'Approved',
                        'oc_product_id' => null,
                        'edatetime' => now(),
                    ]);
            }

            // 4. Record audit log in published_product_update_logs
            try {
                PublishedProductUpdateLog::create([
                    'published_product_id' => $publishedRecord->sno,
                    'specification_id' => $specId,
                    'woocommerce_product_id' => $publishedRecord->woocommerce_product_id ?: $wcProductId,
                    'target_supplier_id' => $publishedRecord->target_supplier_id,
                    'sku' => $spec->sku ?? $sku,
                    'barcode' => $spec->barcode ?? null,
                    'old_stock' => $currentStock,
                    'new_stock' => 0, // Online website catalog stock is now 0 (deleted)
                    'source_channel' => 'other',
                    'reason_notes' => "Product #{$wcProductId} was deleted/trashed on WooCommerce store ({$sourceUrl}). PMS publication status set to 'trashed'. ERP warehouse inventory preserved.",
                    'sync_status' => 'success',
                    'sync_payload' => json_encode($payload),
                    'updated_by' => null,
                    'updated_by_name' => 'WooCommerce Webhook',
                ]);
            } catch (\Throwable $logEx) {
                Log::warning('Could not write PublishedProductUpdateLog on webhook delete: ' . $logEx->getMessage());
            }

            DB::commit();

            Log::info("WooCommerce Product #{$wcProductId} (Spec #{$specId}) successfully marked as trashed in PMS via webhook.");

            return response()->json([
                'success' => true,
                'message' => "Product #{$wcProductId} marked as trashed in PMS. ERP inventory preserved.",
                'specification_id' => $specId,
                'published_id' => $publishedRecord->sno,
                'status' => 'trashed',
                'erp_stock' => $currentStock
            ], 200);

        } catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Process product restore from WooCommerce Trash.
     */
    protected function processProductRestored($wcProductId, $sku, array $payload, string $sourceUrl)
    {
        $publishedRecord = DB::table('published_products')
            ->where('woocommerce_product_id', $wcProductId)
            ->first();

        if (!$publishedRecord) {
            return response()->json([
                'success' => true,
                'message' => "Restored product #{$wcProductId} not tracked in PMS.",
                'woocommerce_product_id' => $wcProductId
            ], 200);
        }

        $specId = $publishedRecord->specification_id;
        $spec = DB::table('auto_designer_specification_master')->where('sno', $specId)->first();

        DB::beginTransaction();
        try {
            DB::table('published_products')
                ->where('sno', $publishedRecord->sno)
                ->update([
                    'status' => 'published',
                    'updated_at' => now(),
                ]);

            DB::table('auto_designer_specification_master')
                ->where('sno', $specId)
                ->update([
                    'status' => 'Published',
                    'oc_product_id' => $wcProductId,
                    'edatetime' => now(),
                ]);

            try {
                PublishedProductUpdateLog::create([
                    'published_product_id' => $publishedRecord->sno,
                    'specification_id' => $specId,
                    'woocommerce_product_id' => $wcProductId,
                    'target_supplier_id' => $publishedRecord->target_supplier_id,
                    'sku' => $spec->sku ?? $sku,
                    'barcode' => $spec->barcode ?? null,
                    'source_channel' => 'other',
                    'reason_notes' => "Product #{$wcProductId} was restored from trash on WooCommerce store ({$sourceUrl}). PMS publication status restored to 'published'.",
                    'sync_status' => 'success',
                    'sync_payload' => json_encode($payload),
                    'updated_by' => null,
                    'updated_by_name' => 'WooCommerce Webhook',
                ]);
            } catch (\Throwable $logEx) {
                Log::warning('Could not write PublishedProductUpdateLog on webhook restore: ' . $logEx->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Product #{$wcProductId} restored to published status in PMS.",
                'specification_id' => $specId,
                'published_id' => $publishedRecord->sno,
                'status' => 'published'
            ], 200);

        } catch (\Throwable $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    /**
     * Process product updates (status, stock, price changed on WooCommerce).
     */
    protected function processProductUpdated($wcProductId, $sku, array $payload, string $sourceUrl)
    {
        $publishedRecord = DB::table('published_products')
            ->where('woocommerce_product_id', $wcProductId)
            ->first();

        if (!$publishedRecord) {
            return response()->json([
                'success' => true,
                'message' => "Product #{$wcProductId} received. Not linked to PMS.",
                'woocommerce_product_id' => $wcProductId
            ], 200);
        }

        $wcStatus = strtolower(trim($payload['status'] ?? ''));
        $stockQty = isset($payload['stock_quantity']) && is_numeric($payload['stock_quantity']) ? (int) $payload['stock_quantity'] : null;

        // If product was trashed on update
        if ($wcStatus === 'trash') {
            return $this->processProductDeleted($wcProductId, $sku, $payload, $sourceUrl);
        }

        // If product was published
        if ($wcStatus === 'publish' && $publishedRecord->status !== 'published') {
            DB::table('published_products')
                ->where('sno', $publishedRecord->sno)
                ->update(['status' => 'published', 'updated_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => "Product #{$wcProductId} webhook updated successfully.",
            'woocommerce_product_id' => $wcProductId,
            'status' => $wcStatus,
            'stock_quantity' => $stockQty
        ], 200);
    }
}
