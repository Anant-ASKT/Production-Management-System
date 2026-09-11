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
            if (!Schema::hasColumn('supplier_products', 'item_name')) {
                $table->string('item_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('supplier_products', 'yarn')) {
                $table->string('yarn')->nullable()->after('colour');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'yarn']);
        });
    }
};
