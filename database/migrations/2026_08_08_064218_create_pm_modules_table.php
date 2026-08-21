<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_modules', function (Blueprint $table) {

            $table->id();

            // Company / Sub Company / Project
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('sub_company_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            // Module Information
            $table->string('module_name', 150);

            $table->string('module_slug', 150);

            $table->string('icon', 100)->nullable();

            $table->integer('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->timestamps();

            // Same module can exist in different companies/projects
            $table->unique(
                ['company_id', 'sub_company_id', 'project_id', 'module_slug'],
                'pm_modules_company_module_unique'
            );

            $table->index('company_id');
            $table->index('sub_company_id');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_modules');
    }
};