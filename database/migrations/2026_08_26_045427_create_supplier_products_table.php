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
        Schema::create('supplier_products', function (Blueprint $table) {
            $table->id('sno');
            $table->unsignedBigInteger('countryid')->nullable();
            $table->unsignedBigInteger('companyid')->nullable();
            $table->unsignedBigInteger('subcompanyid')->nullable();
            $table->unsignedBigInteger('projectid')->nullable();
            $table->unsignedBigInteger('subprojectid')->nullable();
            
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            
            $table->string('main_image')->nullable();
            $table->text('sub_images')->nullable();
            
            // Simple text/varchar fields per requirement
            $table->string('design_names')->nullable();
            $table->string('compositions')->nullable();
            $table->string('mfg_processes')->nullable();
            $table->string('craftsmen')->nullable();
            $table->string('designers')->nullable();
            $table->string('variations')->nullable();
            $table->string('item_type')->nullable();
            $table->string('designer')->nullable();
            $table->string('gender')->nullable();
            $table->string('composition')->nullable();
            $table->string('colour')->nullable();
            $table->string('size')->nullable();
            $table->string('embellishment')->nullable();
            $table->string('manufacturing_process')->nullable();
            $table->string('craftsman')->nullable();
            $table->string('manufacture')->nullable();
            $table->string('collection')->nullable();

            $table->timestamps();
            
            $table->foreign('supplier_id')->references('sno')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_products');
    }
};
