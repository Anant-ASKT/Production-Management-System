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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('nickname', 10)->nullable()->unique()->after('name');
        });

        // Set default nicknames for existing suppliers if any
        $existing = DB::table('suppliers')->get();
        foreach ($existing as $supplier) {
            $cleanName = preg_replace('/[^A-Za-z]/', '', $supplier->name);
            $code = strtoupper(substr($cleanName, 0, 3));
            if (strlen($code) < 3) {
                $code = str_pad($code, 3, 'X');
            }
            // Ensure unique
            $count = DB::table('suppliers')->where('nickname', $code)->count();
            if ($count > 0) {
                $code = strtoupper(substr($code, 0, 2)) . $supplier->sno;
            }
            DB::table('suppliers')->where('sno', $supplier->sno)->update(['nickname' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
};
