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
        Schema::create('enhanced_product_submissions', function (Blueprint $table) {
            $table->id('sno');
            $table->unsignedBigInteger('countryid')->nullable();
            $table->unsignedBigInteger('companyid')->nullable();
            $table->unsignedBigInteger('subcompanyid')->nullable();
            $table->unsignedBigInteger('projectid')->nullable();
            $table->unsignedBigInteger('subprojectid')->nullable();

            $table->unsignedBigInteger('specification_id');
            $table->unsignedBigInteger('ai_photo_enhancer_id');
            $table->string('original_image_path');
            $table->string('enhanced_image_path');
            $table->string('image_type')->default('main'); // main, sub
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enhanced_product_submissions');
    }
};
