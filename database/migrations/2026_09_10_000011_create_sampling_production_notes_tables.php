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
        // 1. First-Class Production Notes
        Schema::create('sampling_production_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->integer('sequence')->default(1);
            $table->string('subject', 191);
            $table->unsignedBigInteger('division_id')->nullable();
            $table->text('written_note')->nullable();
            $table->string('voice_audio_path', 255)->nullable();
            $table->integer('audio_duration_seconds')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('sampling_divisions')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('sampling_users')->onDelete('set null');
        });

        // 2. Production Note Attachments (Sketches, error warning photos, PDFs)
        Schema::create('sampling_production_note_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_note_id');
            $table->string('file_type', 30); // image, sketch, pdf, doc
            $table->string('file_path', 255);
            $table->string('file_name', 191);
            $table->timestamps();

            $table->foreign('production_note_id')->references('id')->on('sampling_production_notes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_production_note_attachments');
        Schema::dropIfExists('sampling_production_notes');
    }
};
