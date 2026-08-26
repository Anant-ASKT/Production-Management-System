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
        Schema::table('auto_designer_specification_master', function (Blueprint $table) {
            $table->boolean('is_sent_to_photo_section')->default(0)->after('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_designer_specification_master', function (Blueprint $table) {
            $table->dropColumn('is_sent_to_photo_section');
        });
    }
};
