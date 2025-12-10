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
        Schema::create('flight_schedule_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_flight_id')->constrained()->onDelete('cascade');
            $table->integer('route_order')->default(1); // Order of the route segment (1st leg, 2nd leg, etc.)
            $table->foreignId('origin_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('destination_airport_id')->constrained('airports')->onDelete('cascade');
            $table->datetime('departure_time');
            $table->datetime('arrival_time');
            $table->datetime('actual_departure_time')->nullable();
            $table->datetime('actual_arrival_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Calculated duration
            $table->decimal('distance_km', 10, 2)->nullable(); // Distance for this segment
            $table->decimal('segment_price', 10, 2)->nullable(); // Price for this segment
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure proper ordering and uniqueness
            $table->unique(['scheduled_flight_id', 'route_order'], 'flight_route_order_unique');
            $table->index(['scheduled_flight_id', 'route_order'], 'flight_route_schedule_idx');
            $table->index(['origin_airport_id', 'destination_airport_id'], 'route_segment_airports_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_schedule_routes');
    }
};
