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
        Schema::create('sampling_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->unique();
            $table->string('contact_person', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_companies');
    }
};
