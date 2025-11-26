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
        Schema::create('proficiency_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->integer('renewal_period_months')->nullable(); // How often it needs renewal
            $table->boolean('has_levels')->default(false); // Like EPL levels 1-6
            $table->json('required_fields')->nullable(); // Which fields are required for this type
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'code']);
        });

        // Insert default proficiency types
        DB::table('proficiency_types')->insert([
            [
                'name' => 'English Proficiency Level (EPL)',
                'code' => 'EPL',
                'description' => 'ICAO English Proficiency Level assessment for aviation personnel',
                'renewal_period_months' => 36,
                'has_levels' => true,
                'required_fields' => json_encode(['epl_level', 'testing_authority', 'issue_date', 'expiry_date']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Instrument Rating (IR)',
                'code' => 'IR',
                'description' => 'Instrument Rating certification',
                'renewal_period_months' => 12,
                'has_levels' => false,
                'required_fields' => json_encode(['rating_type', 'renewal_status', 'issue_date', 'expiry_date']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operator Proficiency Check (OPC)',
                'code' => 'OPC',
                'description' => 'Operator Proficiency Check for commercial pilots',
                'renewal_period_months' => 6,
                'has_levels' => false,
                'required_fields' => json_encode(['examiner_name', 'check_type', 'check_status', 'last_completion_date', 'next_due_date']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Line Check',
                'code' => 'LINE_CHECK',
                'description' => 'Line Check assessment for pilots',
                'renewal_period_months' => 6,
                'has_levels' => false,
                'required_fields' => json_encode(['examiner_name', 'route_sector', 'check_status', 'last_completion_date', 'next_due_date']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dangerous Goods (DG)',
                'code' => 'DG',
                'description' => 'Dangerous Goods handling certification',
                'renewal_period_months' => 24,
                'has_levels' => false,
                'required_fields' => json_encode(['training_provider', 'certificate_number', 'last_completion_date', 'next_due_date']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Safety Management System (SMS)',
                'code' => 'SMS',
                'description' => 'Safety Management System training',
                'renewal_period_months' => 36,
                'has_levels' => false,
                'required_fields' => json_encode(['training_provider', 'certificate_number', 'last_completion_date', 'next_due_date']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Crew Resource Management (CRM)',
                'code' => 'CRM',
                'description' => 'Crew Resource Management training',
                'renewal_period_months' => 36,
                'has_levels' => false,
                'required_fields' => json_encode(['training_provider', 'certificate_number', 'last_completion_date', 'next_due_date']),
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
        Schema::dropIfExists('proficiency_types');
    }
};
