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
        // 1. Operations Master (Standard making steps)
        if (!Schema::hasTable('sampling_operation_masters')) {
            Schema::create('sampling_operation_masters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sampling_company_id');
                $table->string('operation_name', 191);
                $table->unsignedBigInteger('division_id')->nullable();
                $table->string('skill_level', 50)->default('Skilled');
                $table->decimal('default_time_minutes', 8, 2)->default(0.00);
                $table->decimal('default_rate_per_hour', 10, 2)->default(0.00);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
                $table->foreign('division_id')->references('id')->on('sampling_divisions')->onDelete('set null');
                $table->index(['sampling_company_id', 'status'], 'som_comp_status_idx');
            });
        }

        // 2. Materials & Items Master
        if (!Schema::hasTable('sampling_material_masters')) {
            Schema::create('sampling_material_masters', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sampling_company_id');
                $table->string('material_category', 100); // Fabric, Yarn, Thread, Buttons, Zippers, Labels, Lining, Trims, Packaging
                $table->string('material_name', 191);
                $table->string('item_code', 50)->nullable();
                $table->string('unit_of_measure', 30)->default('Pieces');
                $table->decimal('standard_cost', 12, 4)->default(0.0000);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
                $table->index(['sampling_company_id', 'material_category'], 'smm_comp_cat_idx');
            });
        }

        // 3. Units of Measure (UOM)
        if (!Schema::hasTable('sampling_uoms')) {
            Schema::create('sampling_uoms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sampling_company_id');
                $table->string('name', 100);
                $table->string('symbol', 30);
                $table->string('type', 50)->nullable(); // Length, Weight, Quantity, Area
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
                $table->index(['sampling_company_id', 'status'], 'suom_comp_status_idx');
            });
        }

        // 4. Designers Master
        if (!Schema::hasTable('sampling_designers')) {
            Schema::create('sampling_designers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sampling_company_id');
                $table->string('name', 191);
                $table->string('code', 50)->nullable();
                $table->string('email', 191)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('specialization', 100)->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
                $table->index(['sampling_company_id', 'status'], 'sdes_comp_status_idx');
            });
        }

        // 5. Collections / Seasons Master
        if (!Schema::hasTable('sampling_collections')) {
            Schema::create('sampling_collections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sampling_company_id');
                $table->string('name', 191);
                $table->string('code', 50)->nullable();
                $table->string('season', 100)->nullable(); // Autumn / Winter, Spring / Summer, Resort
                $table->string('year', 20)->nullable(); // 2026, 2027
                $table->text('description')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();

                $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
                $table->index(['sampling_company_id', 'status'], 'scoll_comp_status_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_collections');
        Schema::dropIfExists('sampling_designers');
        Schema::dropIfExists('sampling_uoms');
        Schema::dropIfExists('sampling_material_masters');
        Schema::dropIfExists('sampling_operation_masters');
    }
};
