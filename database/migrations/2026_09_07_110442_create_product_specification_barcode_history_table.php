<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_specification_barcode_history', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('companyid');
            $table->unsignedBigInteger('subcompanyid');
            $table->unsignedBigInteger('projectid');

            $table->unsignedBigInteger('original_sno')->nullable();
            $table->unsignedBigInteger('new_sno')->nullable();

            $table->string('old_barcode', 255);
            $table->string('new_barcode', 255);

            $table->string('modified_by', 255)->nullable();
            $table->timestamp('modified_at')->nullable();

            $table->timestamps();

            $table->index(
                ['companyid', 'subcompanyid', 'projectid', 'old_barcode'],
                'psbh_context_old_barcode_idx'
            );

            $table->index(
                ['companyid', 'subcompanyid', 'projectid', 'new_barcode'],
                'psbh_context_new_barcode_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_specification_barcode_history');
    }
};