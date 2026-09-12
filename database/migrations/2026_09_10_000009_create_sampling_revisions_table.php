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
        Schema::create('sampling_revisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->integer('revision_number')->default(0); // 0 for Rev 0, 1 for Rev 1...
            $table->string('revision_code', 60); // e.g. SAM-2027-004-B01-S03-REV0
            $table->dateTime('frozen_date');
            $table->unsignedBigInteger('frozen_by');
            $table->text('change_summary')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('checklist_snapshot_json')->nullable();
            $table->decimal('direct_production_cost', 12, 4)->default(0.0000);
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('frozen_by')->references('id')->on('sampling_users')->onDelete('restrict');
            $table->unique(['sample_id', 'revision_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_revisions');
    }
};
