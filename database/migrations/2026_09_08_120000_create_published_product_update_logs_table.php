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
        Schema::create('published_product_update_logs', function (Blueprint $table) {
            $table->id('sno');
            $table->unsignedBigInteger('published_product_id')->nullable();
            $table->unsignedBigInteger('specification_id');
            $table->unsignedBigInteger('woocommerce_product_id')->nullable();
            $table->unsignedBigInteger('target_supplier_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();

            // Old & New values
            $table->decimal('old_regular_price', 10, 2)->nullable();
            $table->decimal('new_regular_price', 10, 2)->nullable();
            $table->decimal('old_sale_price', 10, 2)->nullable();
            $table->decimal('new_sale_price', 10, 2)->nullable();
            $table->integer('old_stock')->nullable();
            $table->integer('new_stock')->nullable();

            // Communication / Reason
            $table->string('source_channel')->default('whatsapp'); // whatsapp, phone_call, email, other
            $table->text('reason_notes')->nullable();

            // Sync Status
            $table->string('sync_status')->default('success'); // success, failed, local_only
            $table->text('sync_error')->nullable();
            $table->longText('sync_payload')->nullable();
            $table->longText('sync_response')->nullable();

            // Admin who made change
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->string('updated_by_name')->nullable();

            $table->timestamps();

            $table->index('published_product_id');
            $table->index('specification_id');
            $table->index('sku');
            $table->index('barcode');
            $table->index('source_channel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('published_product_update_logs');
    }
};
