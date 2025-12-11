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
        Schema::table('aircraft', function (Blueprint $table) {
            // Weight specifications
            $table->decimal('max_takeoff_weight', 10, 2)->nullable()->after('max_passengers');
            $table->decimal('max_landing_weight', 10, 2)->nullable()->after('max_takeoff_weight');
            $table->decimal('empty_weight', 10, 2)->nullable()->after('max_landing_weight');
            $table->decimal('useful_load', 10, 2)->nullable()->after('empty_weight');

            // Engine and fuel specifications
            $table->string('engine_type')->nullable()->after('useful_load');
            $table->integer('number_of_engines')->nullable()->after('engine_type');
            $table->decimal('fuel_capacity', 10, 2)->nullable()->after('number_of_engines');

            // Performance specifications
            $table->integer('service_ceiling')->nullable()->after('fuel_capacity'); // in feet
            $table->integer('maximum_range')->nullable()->after('service_ceiling'); // in nautical miles
            $table->integer('cruise_speed')->nullable()->after('maximum_range'); // in knots

            // Certification and operational fields
            $table->string('aircraft_category')->nullable()->after('cruise_speed');
            $table->string('certification_basis')->nullable()->after('aircraft_category');
            $table->boolean('ifr_certified')->default(false)->after('certification_basis');
            $table->boolean('rvsm_approved')->default(false)->after('ifr_certified');
            $table->string('etops_rating')->nullable()->after('rvsm_approved');

            // Regulatory fields
            $table->string('certificate_of_airworthiness')->nullable()->after('etops_rating');
            $table->date('coa_issue_date')->nullable()->after('certificate_of_airworthiness');
            $table->date('coa_expiry_date')->nullable()->after('coa_issue_date');

            // Additional specifications
            $table->string('avionics_suite')->nullable()->after('coa_expiry_date');
            $table->string('propeller_type')->nullable()->after('avionics_suite');
            $table->string('noise_certification')->nullable()->after('propeller_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropColumn([
                'max_takeoff_weight',
                'max_landing_weight',
                'empty_weight',
                'useful_load',
                'engine_type',
                'number_of_engines',
                'fuel_capacity',
                'service_ceiling',
                'maximum_range',
                'cruise_speed',
                'aircraft_category',
                'certification_basis',
                'ifr_certified',
                'rvsm_approved',
                'etops_rating',
                'certificate_of_airworthiness',
                'coa_issue_date',
                'coa_expiry_date',
                'avionics_suite',
                'propeller_type',
                'noise_certification'
            ]);
        });
    }
};
