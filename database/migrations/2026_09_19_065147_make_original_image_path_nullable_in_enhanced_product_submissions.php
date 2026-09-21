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
        Schema::table('enhanced_product_submissions', function (Blueprint $table) {
            $table->string('original_image_path')->nullable()->change();
        });

        if (Schema::hasTable('approved_enhanced_images')) {
            Schema::table('approved_enhanced_images', function (Blueprint $table) {
                $table->string('original_image_path')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enhanced_product_submissions', function (Blueprint $table) {
            $table->string('original_image_path')->nullable(false)->change();
        });

        if (Schema::hasTable('approved_enhanced_images')) {
            Schema::table('approved_enhanced_images', function (Blueprint $table) {
                $table->string('original_image_path')->nullable(false)->change();
            });
        }
    }
};
