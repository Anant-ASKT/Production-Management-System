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
        Schema::create('published_products', function (Blueprint $table) {
            $table->id('sno');
            $table->unsignedBigInteger('countryid')->nullable();
            $table->unsignedBigInteger('companyid')->nullable();
            $table->unsignedBigInteger('subcompanyid')->nullable();
            $table->unsignedBigInteger('projectid')->nullable();
            $table->unsignedBigInteger('subprojectid')->nullable();

            $table->unsignedBigInteger('specification_id');
            $table->unsignedBigInteger('origin_supplier_id')->nullable();
            $table->unsignedBigInteger('target_supplier_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_name')->nullable();
            $table->unsignedBigInteger('woocommerce_product_id')->nullable();
            $table->string('permalink', 500)->nullable();
            $table->string('status')->default('published');
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();

            $table->foreign('target_supplier_id')->references('sno')->on('suppliers')->onDelete('cascade');
            $table->foreign('category_id')->references('sno')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('published_products');
    }
};
