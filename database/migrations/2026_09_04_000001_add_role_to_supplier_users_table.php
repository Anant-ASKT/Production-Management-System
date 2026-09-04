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
        if (!Schema::hasColumn('supplier_users', 'role')) {
            Schema::table('supplier_users', function (Blueprint $table) {
                $table->string('role', 50)->default('Owner')->after('phone');
            });

            // Set existing users to Owner if not set
            DB::table('supplier_users')->whereNull('role')->orWhere('role', '')->update(['role' => 'Owner']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('supplier_users', 'role')) {
            Schema::table('supplier_users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
