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
        // Payment Status Reference Table
        Schema::create('payment_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('color')->default('#6c757d'); // Bootstrap color classes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Airport Reference Table
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iata_code', 3)->nullable();
            $table->string('icao_code', 4)->unique();
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('landing_fee_rate', 10, 4)->nullable(); // Per kg or per landing
            $table->string('fee_calculation_method')->default('per_mtow'); // per_mtow, flat_rate
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Service Providers (for navigation fees)
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('country')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->decimal('default_rate_per_km', 8, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fuel Suppliers
        Schema::create('fuel_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->text('payment_terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Fuel Units (Liters, Gallons, etc.)
        Schema::create('fuel_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('symbol')->nullable();
            $table->decimal('conversion_to_liters', 10, 6)->default(1); // For unit conversion
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default data
        DB::table('payment_statuses')->insert([
            ['name' => 'Pending', 'code' => 'pending', 'description' => 'Payment is pending', 'color' => '#ffc107'],
            ['name' => 'Paid', 'code' => 'paid', 'description' => 'Payment completed', 'color' => '#28a745'],
            ['name' => 'Overdue', 'code' => 'overdue', 'description' => 'Payment is overdue', 'color' => '#dc3545'],
            ['name' => 'Disputed', 'code' => 'disputed', 'description' => 'Payment is disputed', 'color' => '#fd7e14'],
            ['name' => 'Cancelled', 'code' => 'cancelled', 'description' => 'Payment cancelled', 'color' => '#6c757d'],
        ]);

        DB::table('fuel_units')->insert([
            ['name' => 'Liters', 'code' => 'L', 'symbol' => 'L', 'conversion_to_liters' => 1.0],
            ['name' => 'US Gallons', 'code' => 'GAL_US', 'symbol' => 'gal', 'conversion_to_liters' => 3.78541],
            ['name' => 'Imperial Gallons', 'code' => 'GAL_IMP', 'symbol' => 'gal (imp)', 'conversion_to_liters' => 4.54609],
            ['name' => 'Kilograms', 'code' => 'KG', 'symbol' => 'kg', 'conversion_to_liters' => 1.25], // Approximate for Jet A-1
        ]);

        // Sample airports
        DB::table('airports')->insert([
            ['name' => 'Julius Nyerere International Airport', 'iata_code' => 'DAR', 'icao_code' => 'HTDA', 'city' => 'Dar es Salaam', 'country' => 'Tanzania', 'landing_fee_rate' => 2.5, 'fee_calculation_method' => 'per_mtow'],
            ['name' => 'Abeid Amani Karume International Airport', 'iata_code' => 'ZNZ', 'icao_code' => 'HTZA', 'city' => 'Zanzibar', 'country' => 'Tanzania', 'landing_fee_rate' => 2.0, 'fee_calculation_method' => 'per_mtow'],
            ['name' => 'Kilimanjaro International Airport', 'iata_code' => 'JRO', 'icao_code' => 'HTKJ', 'city' => 'Moshi', 'country' => 'Tanzania', 'landing_fee_rate' => 3.0, 'fee_calculation_method' => 'per_mtow'],
        ]);

        // Sample service providers
        DB::table('service_providers')->insert([
            ['name' => 'Tanzania Civil Aviation Authority', 'code' => 'TCAA', 'country' => 'Tanzania', 'default_rate_per_km' => 0.15],
            ['name' => 'Kenya Civil Aviation Authority', 'code' => 'KCAA', 'country' => 'Kenya', 'default_rate_per_km' => 0.18],
            ['name' => 'EUROCONTROL', 'code' => 'EUROCONTROL', 'country' => 'Europe', 'default_rate_per_km' => 0.25],
        ]);

        // Sample fuel suppliers
        DB::table('fuel_suppliers')->insert([
            ['name' => 'Total Tanzania', 'code' => 'TOTAL_TZ', 'contact_person' => 'Fuel Manager'],
            ['name' => 'BP Tanzania', 'code' => 'BP_TZ', 'contact_person' => 'Aviation Fuel Division'],
            ['name' => 'Oryx Energies', 'code' => 'ORYX', 'contact_person' => 'Aviation Sales'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_units');
        Schema::dropIfExists('fuel_suppliers');
        Schema::dropIfExists('service_providers');
        Schema::dropIfExists('airports');
        Schema::dropIfExists('payment_statuses');
    }
};
