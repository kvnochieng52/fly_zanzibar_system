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
        // Fuel purchases table
        Schema::create('fuel_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_number')->unique();
            $table->date('purchase_date');
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->foreignId('airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('fuel_supplier_id')->constrained('fuel_suppliers')->onDelete('cascade');
            $table->foreignId('fuel_unit_id')->constrained('fuel_units')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 4);
            $table->decimal('total_amount', 12, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('invoice_reference')->nullable();
            $table->date('invoice_date')->nullable();
            $table->foreignId('payment_status_id')->constrained('payment_statuses');
            $table->date('payment_date')->nullable();
            $table->text('supplier_document')->nullable(); // File path or reference
            $table->string('fuel_grade')->nullable(); // Jet A-1, AvGas, etc.
            $table->decimal('density', 6, 4)->nullable(); // kg/L at 15°C
            $table->decimal('fuel_quality_rating', 3, 2)->nullable(); // Quality score
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['purchase_date', 'aircraft_id']);
            $table->index(['fuel_supplier_id', 'purchase_date']);
            $table->index(['payment_status_id']);
            $table->index(['airport_id', 'fuel_supplier_id']);
        });

        // Fuel consumption table
        Schema::create('fuel_consumption', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->date('flight_date');
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->string('route_from'); // Departure airport ICAO
            $table->string('route_to'); // Arrival airport ICAO
            $table->decimal('fuel_consumed', 10, 2); // Liters or Gallons
            $table->foreignId('fuel_unit_id')->constrained('fuel_units');
            $table->decimal('flight_hours', 6, 2); // Total flight time
            $table->decimal('distance_km', 8, 2); // Flight distance
            $table->decimal('average_fuel_flow', 8, 2)->nullable(); // L/hr or GPH
            $table->decimal('fuel_efficiency', 8, 4)->nullable(); // km/L or mpg
            $table->text('weather_conditions')->nullable();
            $table->integer('passenger_count')->nullable();
            $table->decimal('cargo_weight', 8, 2)->nullable(); // kg
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['flight_date', 'aircraft_id']);
            $table->index(['route_from', 'route_to']);
            $table->unique(['flight_number', 'flight_date'], 'fuel_consumption_flight_unique');
        });

        // Fuel account balances table
        Schema::create('fuel_account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->foreignId('airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('fuel_supplier_id')->constrained('fuel_suppliers')->onDelete('cascade');
            $table->foreignId('fuel_unit_id')->constrained('fuel_units');
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('purchased_quantity', 10, 2)->default(0);
            $table->decimal('consumed_quantity', 10, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->decimal('minimum_threshold', 10, 2)->default(0); // Alert threshold
            $table->date('last_purchase_date')->nullable();
            $table->date('last_consumption_date')->nullable();
            $table->decimal('average_consumption_rate', 8, 2)->nullable(); // Per day/hour
            $table->integer('estimated_days_remaining')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['aircraft_id', 'airport_id']);
            $table->index(['fuel_supplier_id']);
            $table->index(['current_balance']);
            // Shortened constraint name to fit MySQL 64-character limit
            $table->unique(['aircraft_id', 'airport_id', 'fuel_supplier_id'], 'fuel_balance_aircraft_airport_supplier_unique');
        });

        // Fuel cost allocations table
        Schema::create('fuel_cost_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_purchase_id')->constrained('fuel_purchases')->onDelete('cascade');
            $table->foreignId('fuel_consumption_id')->constrained('fuel_consumption')->onDelete('cascade');
            $table->decimal('allocated_quantity', 10, 2); // Quantity allocated from purchase
            $table->decimal('unit_cost', 10, 4); // Cost per unit from purchase
            $table->decimal('allocated_cost', 12, 2); // Total allocated cost
            $table->date('allocation_date');
            $table->string('allocation_method'); // FIFO, LIFO, Weighted Average
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['fuel_purchase_id']);
            $table->index(['fuel_consumption_id']);
            $table->index(['allocation_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_cost_allocations');
        Schema::dropIfExists('fuel_account_balances');
        Schema::dropIfExists('fuel_consumption');
        Schema::dropIfExists('fuel_purchases');
    }
};
