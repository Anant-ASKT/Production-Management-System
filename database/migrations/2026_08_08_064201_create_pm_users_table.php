<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_users', function (Blueprint $table) {

            $table->id();

            // Company / Sub Company / Project
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('sub_company_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            // User Information
            $table->string('name', 150);

            $table->string('username', 100);

            $table->string('email', 150)->nullable();

            $table->string('password');

            // admin / user
            $table->enum('role', ['admin', 'user'])
                  ->default('user');

            $table->boolean('status')
                  ->default(true);

            $table->rememberToken();

            $table->timestamps();

            // Same username can exist in different companies/projects
            $table->unique(
                ['company_id', 'sub_company_id', 'project_id', 'username'],
                'pm_users_company_user_unique'
            );

            // Useful indexes
            $table->index('company_id');
            $table->index('sub_company_id');
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_users');
    }
};