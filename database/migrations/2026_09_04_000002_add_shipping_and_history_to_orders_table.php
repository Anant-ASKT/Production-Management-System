<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add shipping & fulfillment fields to order_webhook_payloads
        Schema::table('order_webhook_payloads', function (Blueprint $table) {
            if (!Schema::hasColumn('order_webhook_payloads', 'courier_name')) {
                $table->string('courier_name', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('order_webhook_payloads', 'tracking_id')) {
                $table->string('tracking_id', 100)->nullable()->after('courier_name');
            }
            if (!Schema::hasColumn('order_webhook_payloads', 'tracking_url')) {
                $table->text('tracking_url')->nullable()->after('tracking_id');
            }
            if (!Schema::hasColumn('order_webhook_payloads', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('tracking_url');
            }
            if (!Schema::hasColumn('order_webhook_payloads', 'shipping_notes')) {
                $table->text('shipping_notes')->nullable()->after('shipped_at');
            }
        });

        // 2. Create order_histories table for comprehensive audit logs
        if (!Schema::hasTable('order_histories')) {
            Schema::create('order_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_webhook_payload_id');
                $table->string('user_type', 50)->default('supplier'); // 'supplier', 'admin', 'system'
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name')->nullable();
                $table->string('action', 100); // 'Status Changed', 'Tracking Added', 'Order Updated', etc.
                $table->string('from_status', 50)->nullable();
                $table->string('to_status', 50)->nullable();
                $table->string('courier_name', 100)->nullable();
                $table->string('tracking_id', 100)->nullable();
                $table->text('comment')->nullable();
                $table->timestamps();

                $table->foreign('order_webhook_payload_id')
                      ->references('id')
                      ->on('order_webhook_payloads')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_histories');

        Schema::table('order_webhook_payloads', function (Blueprint $table) {
            $table->dropColumn([
                'courier_name',
                'tracking_id',
                'tracking_url',
                'shipped_at',
                'shipping_notes'
            ]);
        });
    }
};
