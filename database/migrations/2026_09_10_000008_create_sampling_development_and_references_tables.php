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
        // 1. Assignment History (preserves history when responsible person changes)
        Schema::create('sampling_assignment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('assigned_to_user_id');
            $table->unsignedBigInteger('assigned_by_user_id')->nullable();
            $table->date('assigned_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('assigned_to_user_id')->references('id')->on('sampling_users')->onDelete('cascade');
            $table->foreign('assigned_by_user_id')->references('id')->on('sampling_users')->onDelete('set null');
        });

        // 2. Inspiration and Reference Materials (Images, sketches, PDFs, URLs - kept distinct from final documentation)
        Schema::create('sampling_reference_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->string('reference_type', 50); // sketch, photograph, pdf, document, web_url, video_url, yarn_ref, fabric_ref, other
            $table->string('title', 191);
            $table->text('description')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('sampling_users')->onDelete('set null');
        });

        // 3. Optional Physical Development Attempts (Attempt 1, 2, 3...)
        Schema::create('sampling_development_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->integer('attempt_number')->default(1);
            $table->date('attempt_date');
            $table->text('notes')->nullable();
            $table->enum('result_status', ['inconclusive', 'reworked', 'passed', 'failed'])->default('inconclusive');
            $table->text('photos_json')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('sampling_users')->onDelete('set null');
            $table->unique(['sample_id', 'attempt_number']);
        });

        // 4. Sample Approval Submissions & Audit
        Schema::create('sampling_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('submitted_by');
            $table->dateTime('submitted_date');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->dateTime('review_date')->nullable();
            $table->text('review_comments')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approval_date')->nullable();
            $table->enum('status', ['submitted', 'needs_revision', 'approved', 'rejected'])->default('submitted');
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('sampling_users')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('sampling_users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('sampling_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_approvals');
        Schema::dropIfExists('sampling_development_attempts');
        Schema::dropIfExists('sampling_reference_materials');
        Schema::dropIfExists('sampling_assignment_histories');
    }
};
