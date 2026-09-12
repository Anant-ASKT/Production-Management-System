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
        Schema::create('sampling_divisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id')->nullable();
            $table->string('name', 100);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
            $table->index(['sampling_company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_divisions');
    }
};
