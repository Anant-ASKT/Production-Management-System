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
            if (!Schema::hasColumn('supplier_products', 'is_integrated')) {
                $table->boolean('is_integrated')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_products', 'is_integrated')) {
                $table->dropColumn('is_integrated');
            }
        });
    }
};
