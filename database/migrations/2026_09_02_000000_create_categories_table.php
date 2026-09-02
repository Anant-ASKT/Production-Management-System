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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('sno');
            $table->unsignedBigInteger('countryid')->nullable();
            $table->unsignedBigInteger('companyid')->nullable();
            $table->unsignedBigInteger('subcompanyid')->nullable();
            $table->unsignedBigInteger('projectid')->nullable();
            $table->unsignedBigInteger('subprojectid')->nullable();

            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('supplier_id')->references('sno')->on('suppliers')->onDelete('cascade');
            $table->foreign('parent_id')->references('sno')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
