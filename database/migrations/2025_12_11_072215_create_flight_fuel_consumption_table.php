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
        Schema::create('flight_fuel_consumption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_flight_id')->constrained('scheduled_flights')->onDelete('cascade');
            $table->decimal('fuel_consumed', 10, 2); // Fuel consumed amount
            $table->string('fuel_unit', 20)->default('liters'); // liters, gallons, kg
            $table->decimal('distance_km', 10, 2)->nullable(); // Distance covered in kilometers
            $table->decimal('flight_time_hours', 8, 2)->nullable(); // Actual flight time in hours
            $table->decimal('bloc_time_hours', 8, 2)->nullable(); // Bloc time in hours
            $table->decimal('total_time_hours', 8, 2)->nullable(); // Total time in hours
            $table->decimal('fuel_burn_rate', 8, 2)->nullable(); // Fuel burn rate per hour
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for performance
            $table->index('scheduled_flight_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_fuel_consumption');
    }
};
