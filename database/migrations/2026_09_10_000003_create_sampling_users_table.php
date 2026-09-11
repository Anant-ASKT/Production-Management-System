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
        Schema::create('sampling_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id');
            $table->string('name', 191);
            $table->string('email', 191)->unique();
            $table->string('password', 255);
            $table->string('phone', 50)->nullable();
            $table->string('role', 50)->default('sampling_staff'); // company_admin, designer, sampling_manager, sampling_staff, division_head, approver, viewer
            $table->unsignedBigInteger('division_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('sampling_divisions')->onDelete('set null');
            $table->index(['sampling_company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_users');
    }
};
