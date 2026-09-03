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
        Schema::table('supplier_products', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_products', 'min_price')) {
                $table->decimal('min_price', 10, 2)->nullable()->after('sale_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_products', 'min_price')) {
                $table->dropColumn('min_price');
            }
        });
    }
};
