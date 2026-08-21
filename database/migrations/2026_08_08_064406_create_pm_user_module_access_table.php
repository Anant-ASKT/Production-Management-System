<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_user_module_access', function (Blueprint $table) {

            $table->id();

            // Company / Sub Company / Project
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('sub_company_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            // Relations
            $table->foreignId('user_id')
                  ->constrained('pm_users')
                  ->cascadeOnDelete();

            $table->foreignId('module_id')
                  ->constrained('pm_modules')
                  ->cascadeOnDelete();

            // Permissions
            $table->boolean('can_view')->default(true);

            $table->boolean('can_add')->default(false);

            $table->boolean('can_edit')->default(false);

            $table->boolean('can_delete')->default(false);

            $table->timestamps();

            // One user should have one permission record
            // for one module within a company/project
            $table->unique(
                [
                    'company_id',
                    'sub_company_id',
                    'project_id',
                    'user_id',
                    'module_id'
                ],
                'pm_user_module_access_unique'
            );

            $table->index('company_id');
            $table->index('sub_company_id');
            $table->index('project_id');
            $table->index('user_id');
            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_user_module_access');
    }
};