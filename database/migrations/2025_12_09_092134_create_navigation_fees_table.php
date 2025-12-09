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
        Schema::create('navigation_fees', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->date('flight_date');
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->string('route_from'); // Departure airport ICAO
            $table->string('route_to'); // Arrival airport ICAO
            $table->string('route_waypoints')->nullable(); // Intermediate waypoints
            $table->decimal('distance_km', 8, 2); // Route distance in kilometers
            $table->decimal('aircraft_mtow', 8, 2); // Aircraft MTOW
            $table->decimal('fee_amount', 10, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained('service_providers');
            $table->foreignId('payment_status_id')->constrained('payment_statuses');
            $table->date('payment_date')->nullable();
            $table->string('invoice_reference')->nullable();
            $table->text('provider_document')->nullable(); // File path or reference
            $table->text('billing_details')->nullable(); // Additional billing information
            $table->text('notes')->nullable();
            $table->boolean('is_calculated_auto')->default(false);
            $table->decimal('rate_per_km', 8, 4)->nullable(); // Rate used for calculation
            $table->decimal('manual_override_amount', 10, 2)->nullable();
            $table->string('override_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['flight_date', 'aircraft_id']);
            $table->index(['service_provider_id', 'flight_date']);
            $table->index(['payment_status_id']);
            $table->index(['route_from', 'route_to']);
            $table->unique(['flight_number', 'flight_date', 'service_provider_id'], 'nav_fees_flight_provider_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('navigation_fees');
    }
};
