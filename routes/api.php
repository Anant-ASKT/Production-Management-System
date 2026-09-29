<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and are assigned the "api"
| middleware group.
|
*/

// WooCommerce Order Webhook Endpoint
Route::post('/orders/webhook', [OrderWebhookController::class, 'handle'])->name('api.orders.webhook');
Route::post('/order-webhook', [OrderWebhookController::class, 'handle']);
Route::post('/order_webhook_payloads', [OrderWebhookController::class, 'handle']);
