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
        Schema::table('staff', function (Blueprint $table) {
            // Add staff number and remove employee_id dependency
            $table->string('staff_number', 20)->unique()->after('id');

            // Replace nationality string with country relationship
            $table->foreignId('country_id')->nullable()->after('gender_id')->constrained('countries');

            // Add employment type relationship
            $table->foreignId('employment_type_id')->nullable()->after('status_id')->constrained('employment_types');

            // Add work location relationship
            $table->foreignId('work_location_id')->nullable()->after('current_base')->constrained('work_locations');

            // Contract dates for contract staff
            $table->date('contract_start_date')->nullable()->after('hire_date');

            // Supervisor/Manager
            $table->string('supervisor_name', 200)->nullable()->after('work_location_id');

            // Identification details
            $table->string('identification_type', 50)->nullable()->after('notes'); // Passport, ID Card, etc.
            $table->string('identification_number', 50)->nullable()->after('identification_type');
            $table->string('identification_file')->nullable()->after('identification_number');

            // Education qualification
            $table->foreignId('highest_qualification_id')->nullable()->after('identification_file')->constrained('qualifications');
            $table->string('qualification_name', 200)->nullable()->after('highest_qualification_id');
            $table->string('institution_name', 200)->nullable()->after('qualification_name');
            $table->year('graduation_year')->nullable()->after('institution_name');

            // Next of kin details
            $table->string('next_of_kin_name', 200)->nullable()->after('graduation_year');
            $table->string('next_of_kin_phone', 20)->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_email', 150)->nullable()->after('next_of_kin_phone');
            $table->string('next_of_kin_relationship', 50)->nullable()->after('next_of_kin_email');

            // Add indexes
            $table->index('staff_number');
            $table->index('country_id');
            $table->index('employment_type_id');
            $table->index('work_location_id');
            $table->index('highest_qualification_id');
            $table->index('identification_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            // Remove new columns in reverse order
            $table->dropIndex(['identification_type']);
            $table->dropIndex(['highest_qualification_id']);
            $table->dropIndex(['work_location_id']);
            $table->dropIndex(['employment_type_id']);
            $table->dropIndex(['country_id']);
            $table->dropIndex(['staff_number']);

            $table->dropColumn([
                'next_of_kin_relationship',
                'next_of_kin_email',
                'next_of_kin_phone',
                'next_of_kin_name',
                'graduation_year',
                'institution_name',
                'qualification_name',
                'highest_qualification_id',
                'identification_file',
                'identification_number',
                'identification_type',
                'supervisor_name',
                'work_location_id',
                'contract_start_date',
                'employment_type_id',
                'country_id',
                'staff_number'
            ]);
        });
    }
};
