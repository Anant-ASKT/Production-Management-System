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
        if (!Schema::hasTable('sampling_reference_types')) {
            Schema::create('sampling_reference_types', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sampling_company_id');
                $table->string('name', 100);
                $table->string('code', 50)->nullable();
                $table->string('description', 255)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
                $table->index(['sampling_company_id', 'status'], 'srt_comp_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_reference_types');
    }
};
