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
        Schema::table('ai_photo_enhancer_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_photo_enhancer_assignments', 'admin_comment')) {
                $table->text('admin_comment')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_photo_enhancer_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('ai_photo_enhancer_assignments', 'admin_comment')) {
                $table->dropColumn('admin_comment');
            }
        });
    }
};
