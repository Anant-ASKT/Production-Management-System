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
        if (Schema::hasTable('supplier_products')) {
            Schema::table('supplier_products', function (Blueprint $table) {
                if (!Schema::hasColumn('supplier_products', 'is_set')) {
                    $table->boolean('is_set')->default(0)->after('is_integrated');
                }
                if (!Schema::hasColumn('supplier_products', 'set_items')) {
                    $table->longText('set_items')->nullable()->after('is_set');
                }
            });
        }

        if (Schema::hasTable('auto_designer_specification_master')) {
            Schema::table('auto_designer_specification_master', function (Blueprint $table) {
                if (!Schema::hasColumn('auto_designer_specification_master', 'is_set')) {
                    $table->boolean('is_set')->default(0)->nullable();
                }
                if (!Schema::hasColumn('auto_designer_specification_master', 'set_items')) {
                    $table->longText('set_items')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('supplier_products')) {
            Schema::table('supplier_products', function (Blueprint $table) {
                if (Schema::hasColumn('supplier_products', 'set_items')) {
                    $table->dropColumn('set_items');
                }
                if (Schema::hasColumn('supplier_products', 'is_set')) {
                    $table->dropColumn('is_set');
                }
            });
        }

        if (Schema::hasTable('auto_designer_specification_master')) {
            Schema::table('auto_designer_specification_master', function (Blueprint $table) {
                if (Schema::hasColumn('auto_designer_specification_master', 'set_items')) {
                    $table->dropColumn('set_items');
                }
                if (Schema::hasColumn('auto_designer_specification_master', 'is_set')) {
                    $table->dropColumn('is_set');
                }
            });
        }
    }
};
