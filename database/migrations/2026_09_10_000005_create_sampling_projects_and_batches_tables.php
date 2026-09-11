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
        // Sampling Projects
        Schema::create('sampling_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id');
            $table->string('project_code', 50)->unique();
            $table->string('project_name', 191);
            $table->unsignedBigInteger('designer_id')->nullable();
            $table->unsignedBigInteger('collection_id')->nullable();
            $table->date('start_date');
            $table->date('target_completion_date')->nullable();
            $table->date('final_completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'open', 'in_development', 'partially_approved', 'completed', 'closed'])->default('open');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('sampling_users')->onDelete('set null');
            $table->index(['sampling_company_id', 'status']);
        });

        // Sampling Batches
        Schema::create('sampling_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_project_id');
            $table->string('batch_number', 20); // B01, B02
            $table->string('batch_name', 191);
            $table->date('start_date');
            $table->date('target_date')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('sampling_project_id')->references('id')->on('sampling_projects')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('sampling_users')->onDelete('set null');
            $table->unique(['sampling_project_id', 'batch_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_batches');
        Schema::dropIfExists('sampling_projects');
    }
};
