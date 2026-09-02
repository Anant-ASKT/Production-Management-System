<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_users', function (Blueprint $table) {
            $table->id('sno');
            $table->unsignedBigInteger('countryid')->nullable();
            $table->unsignedBigInteger('companyid')->nullable();
            $table->unsignedBigInteger('subcompanyid')->nullable();
            $table->unsignedBigInteger('projectid')->nullable();
            $table->unsignedBigInteger('subprojectid')->nullable();

            $table->unsignedBigInteger('supplier_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('status')->default('active');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('supplier_id')->references('sno')->on('suppliers')->onDelete('cascade');
        });

        // Migrate existing suppliers into supplier_users table
        $existingSuppliers = DB::table('suppliers')->get();
        foreach ($existingSuppliers as $supplier) {
            if (!empty($supplier->email) && !empty($supplier->password)) {
                DB::table('supplier_users')->insert([
                    'supplier_id'   => $supplier->sno,
                    'name'          => $supplier->name,
                    'email'         => $supplier->email,
                    'password'      => $supplier->password,
                    'phone'         => $supplier->phone ?? null,
                    'status'        => 'active',
                    'countryid'     => $supplier->countryid ?? null,
                    'companyid'     => $supplier->companyid ?? null,
                    'subcompanyid'  => $supplier->subcompanyid ?? null,
                    'projectid'     => $supplier->projectid ?? null,
                    'subprojectid'  => $supplier->subprojectid ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_users');
    }
};
