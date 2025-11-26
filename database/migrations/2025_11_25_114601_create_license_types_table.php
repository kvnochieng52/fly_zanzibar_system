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
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->integer('renewal_period_months')->nullable();
            $table->json('required_fields')->nullable();
            $table->boolean('requires_medical')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'code']);
        });

        // Insert default license types
        DB::table('license_types')->insert([
            [
                'name' => 'Commercial Pilot License',
                'code' => 'CPL',
                'description' => 'Commercial Pilot License for commercial operations',
                'renewal_period_months' => 24,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Airline Transport Pilot License',
                'code' => 'ATPL',
                'description' => 'Airline Transport Pilot License for airline operations',
                'renewal_period_months' => 24,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Private Pilot License',
                'code' => 'PPL',
                'description' => 'Private Pilot License for private operations',
                'renewal_period_months' => 24,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Instrument Rating',
                'code' => 'IR',
                'description' => 'Instrument Rating qualification',
                'renewal_period_months' => 12,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Multi-Engine Piston',
                'code' => 'MEP',
                'description' => 'Multi-Engine Piston rating',
                'renewal_period_months' => 24,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Single Engine Piston',
                'code' => 'SEP',
                'description' => 'Single Engine Piston rating',
                'renewal_period_months' => 24,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'RNAV Approach',
                'code' => 'RNAV',
                'description' => 'RNAV Approach qualification',
                'renewal_period_months' => 12,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date', 'expiry_date']),
                'requires_medical' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'code' => 'OTHER',
                'description' => 'Other aviation licenses and certifications',
                'renewal_period_months' => null,
                'required_fields' => json_encode(['license_number', 'issuing_authority', 'issue_date']),
                'requires_medical' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_types');
    }
};
