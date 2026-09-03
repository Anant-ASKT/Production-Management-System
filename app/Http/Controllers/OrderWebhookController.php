<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderWebhookPayload;
use Illuminate\Support\Facades\Log;

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
            $orderKey = $payload['order_key'] ?? null;
            $status = $payload['status'] ?? null;

            // Save payload directly to order_webhook_payloads table
            $record = OrderWebhookPayload::create([
                'order_id' => $orderId ? (string) $orderId : null,
                'order_key' => $orderKey,
                'status' => $status,
                'payload' => $payload,
                'headers' => $request->headers->all(),
            ]);

            Log::info("WooCommerce Order Webhook received and saved successfully.", [
                'record_id' => $record->id,
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order webhook payload saved successfully.',
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
}
