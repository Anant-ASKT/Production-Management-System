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
        // 1. Bill of Materials (BOM)
        Schema::create('sampling_boms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable(); // references item_name_masters or inventory if applicable
            $table->string('material_category', 100);
            $table->string('description', 255);
            $table->string('colour', 100)->nullable();
            $table->string('shade', 100)->nullable();
            $table->string('dye_lot', 100)->nullable();
            $table->string('unit_of_measure', 30);
            $table->decimal('net_quantity', 12, 4);
            $table->decimal('wastage_percentage', 5, 2)->default(0.00);
            $table->decimal('gross_quantity', 12, 4); // net_quantity * (1 + wastage / 100)
            $table->decimal('cost_rate', 12, 4)->default(0.0000);
            $table->decimal('material_cost', 12, 4)->default(0.0000); // gross_quantity * cost_rate
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
        });

        // 2. Labour and Operations
        Schema::create('sampling_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->integer('sequence_number')->default(1);
            $table->string('operation_name', 191);
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('skill_level', 50)->nullable();
            $table->decimal('estimated_time_minutes', 8, 2);
            $table->decimal('labour_rate_per_hour', 10, 2)->default(0.00);
            $table->decimal('estimated_labour_cost', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('sampling_divisions')->onDelete('set null');
        });

        // 3. Technical Specifications
        Schema::create('sampling_specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->string('attribute_name', 100);
            $table->string('attribute_value', 255);
            $table->string('unit', 30)->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
        });

        // 4. Measurements
        Schema::create('sampling_measurements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->unsignedBigInteger('measurement_point_id')->nullable();
            $table->string('point_name', 100);
            $table->decimal('spec_value', 8, 2);
            $table->string('unit', 20)->default('cm');
            $table->decimal('tolerance_plus', 6, 2)->default(0.00);
            $table->decimal('tolerance_minus', 6, 2)->default(0.00);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('measurement_point_id')->references('id')->on('sampling_measurement_points')->onDelete('set null');
        });

        // 5. Pattern Library Linkage
        Schema::create('sampling_patterns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->string('pattern_code', 50)->nullable();
            $table->string('pattern_name', 191);
            $table->string('pattern_type', 50); // sewing, knitting, crochet, embroidery, weaving, cutting_template, leather, other
            $table->string('version', 20)->default('1.0');
            $table->string('size_label', 50)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('sampling_users')->onDelete('set null');
        });

        // 6. Final Technical Photographs and Drawings
        Schema::create('sampling_final_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sample_id');
            $table->unsignedBigInteger('sampling_revision_id')->nullable();
            $table->string('image_type', 50); // front, back, side, detail, inside, construction_detail, stitch_detail, flat_lay, model, line_drawing, label_position, other
            $table->string('file_path', 255);
            $table->string('caption', 255)->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('sample_id')->references('id')->on('sampling_samples')->onDelete('cascade');
            $table->foreign('sampling_revision_id')->references('id')->on('sampling_revisions')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('sampling_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_final_images');
        Schema::dropIfExists('sampling_patterns');
        Schema::dropIfExists('sampling_measurements');
        Schema::dropIfExists('sampling_specifications');
        Schema::dropIfExists('sampling_operations');
        Schema::dropIfExists('sampling_boms');
    }
};
