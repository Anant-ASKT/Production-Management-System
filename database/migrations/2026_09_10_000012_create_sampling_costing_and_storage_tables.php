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
        // 1. Direct Production Cost Calculation
        Schema::create('sampling_costings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->decimal('material_cost_total', 12, 4)->default(0.0000);
            $table->decimal('labour_cost_total', 12, 4)->default(0.0000);
            $table->decimal('dyeing_cost', 12, 4)->default(0.0000);
            $table->decimal('washing_processing_cost', 12, 4)->default(0.0000);
            $table->decimal('trims_accessories_cost', 12, 4)->default(0.0000);
            $table->decimal('outside_services_cost', 12, 4)->default(0.0000);
            $table->decimal('other_direct_cost', 12, 4)->default(0.0000);
            $table->decimal('total_estimated_direct_cost', 12, 4)->default(0.0000);
            $table->string('costing_method', 50)->default('standard');
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
        });

        // 2. Physical Sample Storage Archive
        Schema::create('sampling_physical_storages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->unsignedBigInteger('storage_location_id');
            $table->date('date_stored');
            $table->unsignedBigInteger('stored_by')->nullable();
            $table->string('sample_condition', 100)->default('Approved Master');
            $table->integer('quantity')->default(1);
            $table->string('barcode_qr', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('storage_location_id')->references('id')->on('sampling_storage_locations')->onDelete('restrict');
            $table->foreign('stored_by')->references('id')->on('sampling_users')->onDelete('set null');
        });

        // 3. Production Learning Notes (Feedback from bulk manufacturing against Frozen Revisions)
        Schema::create('sampling_production_learning_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_revision_id');
            $table->unsignedBigInteger('production_order_id')->nullable();
            $table->text('written_note');
            $table->string('voice_audio_path', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'incorporated_in_revision'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('review_date')->nullable();
            $table->text('review_comments')->nullable();
            $table->timestamps();

            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('sampling_users')->onDelete('set null');
            $table->foreign('reviewed_by')->references('id')->on('sampling_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_production_learning_notes');
        Schema::dropIfExists('sampling_physical_storages');
        Schema::dropIfExists('sampling_costings');
    }
};
