<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_user_id')->nullable()->after('supplier_id');
            $table->foreign('supplier_user_id')->references('sno')->on('supplier_users')->onDelete('set null');
        });

        // Populate supplier_user_id for existing products using first user of each supplier
        $suppliers = DB::table('suppliers')->get();
        foreach ($suppliers as $supplier) {
            $firstUser = DB::table('supplier_users')->where('supplier_id', $supplier->sno)->first();
            if ($firstUser) {
                DB::table('supplier_products')
                    ->where('supplier_id', $supplier->sno)
                    ->whereNull('supplier_user_id')
                    ->update(['supplier_user_id' => $firstUser->sno]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_products', function (Blueprint $table) {
            $table->dropForeign(['supplier_user_id']);
            $table->dropColumn('supplier_user_id');
        });
    }
};
