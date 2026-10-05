<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('physical_shop_order_baskets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('sub_company_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('basket_name', 150);
            $table->string('status', 20)->default('draft')->index(); // draft, confirmed
            $table->string('order_number', 50)->nullable()->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'sub_company_id', 'project_id', 'status'], 'psob_tenant_status_idx');
        });

        Schema::create('physical_shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('basket_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('sub_company_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->string('barcode', 150)->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('sku', 150)->nullable();
            $table->string('product_name', 255)->nullable();
            $table->string('item_type_name', 150)->nullable();
            $table->string('gender_name', 100)->nullable();
            $table->string('composition_name', 150)->nullable();
            $table->string('size_name', 100)->nullable();
            $table->string('image_path', 1000)->nullable();
            $table->timestamps();
            $table->foreign('basket_id')->references('id')->on('physical_shop_order_baskets')->cascadeOnDelete();
            $table->unique(['basket_id', 'barcode'], 'psob_item_barcode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_shop_order_items');
        Schema::dropIfExists('physical_shop_order_baskets');
    }
};
