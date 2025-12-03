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
        Schema::create('company_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('requires_expiry')->default(true);
            $table->integer('default_validity_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default document types
        DB::table('company_document_types')->insert([
            ['name' => 'Operating Certificate', 'code' => 'OC', 'description' => 'Operating Certificate', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'Insurance Policy', 'code' => 'IP', 'description' => 'Insurance Policy', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'AOC (Air Operator Certificate)', 'code' => 'AOC', 'description' => 'Air Operator Certificate', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'Regulatory Approval', 'code' => 'RA', 'description' => 'Regulatory Approval', 'requires_expiry' => true, 'default_validity_months' => 24],
            ['name' => 'Contract', 'code' => 'CON', 'description' => 'Contract', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'Lease Agreement', 'code' => 'LA', 'description' => 'Lease Agreement', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'Operations Manual (OM)', 'code' => 'OM', 'description' => 'Operations Manual', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'Flight Operations Manual (FOM)', 'code' => 'FOM', 'description' => 'Flight Operations Manual', 'requires_expiry' => true, 'default_validity_months' => 12],
            ['name' => 'Minimum Equipment List (MEL)', 'code' => 'MEL', 'description' => 'Minimum Equipment List', 'requires_expiry' => true, 'default_validity_months' => 12],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_document_types');
    }
};
