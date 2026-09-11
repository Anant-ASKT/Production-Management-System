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
        Schema::create('sampling_samples', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id');
            $table->unsignedBigInteger('sampling_project_id');
            $table->unsignedBigInteger('sampling_batch_id');
            $table->string('sample_code', 50)->unique(); // e.g. SAM-2027-004-B01-S03
            $table->string('style_name', 191);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->text('description')->nullable();
            
            // Exactly ONE overall responsible division
            $table->unsignedBigInteger('overall_division_id');
            
            // Assigned responsible person
            $table->unsignedBigInteger('assigned_person_id')->nullable();
            $table->date('date_assigned')->nullable();
            
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            
            // Approval & Freeze states
            $table->enum('approval_status', ['pending', 'submitted', 'reviewed', 'approved', 'rejected'])->default('pending');
            $table->date('approval_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            
            $table->boolean('is_frozen')->default(false);
            $table->unsignedBigInteger('current_revision_id')->nullable();
            
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->text('general_notes')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
            $table->foreign('sampling_project_id')->references('id')->on('sampling_projects')->onDelete('cascade');
            $table->foreign('sampling_batch_id')->references('id')->on('sampling_batches')->onDelete('cascade');
            $table->foreign('overall_division_id')->references('id')->on('sampling_divisions')->onDelete('restrict');
            $table->foreign('assigned_person_id')->references('id')->on('sampling_users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('sampling_users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('sampling_users')->onDelete('set null');

            $table->index(['sampling_company_id', 'approval_status', 'is_frozen'], 'smp_cmp_app_frz_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_samples');
    }
};
