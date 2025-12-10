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
        Schema::create('scheduled_flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_code', 20)->unique(); // e.g. FZ101
            $table->foreignId('aircraft_id')->constrained()->onDelete('cascade');
            $table->foreignId('origin_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('destination_airport_id')->constrained('airports')->onDelete('cascade');
            $table->foreignId('flight_status_id')->constrained()->onDelete('cascade');
            $table->datetime('departure_time');
            $table->datetime('arrival_time');
            $table->datetime('actual_departure_time')->nullable();
            $table->datetime('actual_arrival_time')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('available_seats')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['departure_time', 'flight_status_id'], 'flight_schedule_idx');
            $table->index(['aircraft_id', 'departure_time'], 'aircraft_schedule_idx');
            $table->index(['origin_airport_id', 'destination_airport_id'], 'route_airports_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_flights');
    }
};
