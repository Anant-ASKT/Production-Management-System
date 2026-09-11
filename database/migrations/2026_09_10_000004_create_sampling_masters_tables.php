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
        // 1. Measurement Points Master
        Schema::create('sampling_measurement_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('point_name', 100);
            $table->string('code', 50)->nullable();
            $table->string('default_unit', 20)->default('cm');
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
            $table->index(['sampling_company_id', 'category_id'], 'smp_comp_cat_idx');
        });

        // 2. Storage Locations Master (Delhi Studio -> Sample Room -> Rack B -> Shelf 4 -> Box 17)
        Schema::create('sampling_storage_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id')->nullable();
            $table->string('studio_name', 100);
            $table->string('room', 100);
            $table->string('rack', 50)->nullable();
            $table->string('shelf', 50)->nullable();
            $table->string('box', 50)->nullable();
            $table->string('full_location_code', 150)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
        });

        // 3. Technical Specification Templates & Attributes
        Schema::create('sampling_spec_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sampling_company_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('attribute_name', 100);
            $table->string('field_type', 30)->default('text'); // text, number, select
            $table->text('options_json')->nullable();
            $table->string('unit', 30)->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->foreign('sampling_company_id')->references('id')->on('sampling_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_spec_attributes');
        Schema::dropIfExists('sampling_storage_locations');
        Schema::dropIfExists('sampling_measurement_points');
    }
};
