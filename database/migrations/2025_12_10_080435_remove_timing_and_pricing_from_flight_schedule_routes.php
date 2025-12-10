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
        Schema::table('flight_schedule_routes', function (Blueprint $table) {
            // Remove timing and pricing fields
            $table->dropColumn([
                'departure_time',
                'arrival_time',
                'distance_km',
                'segment_price',
                'duration_minutes'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_schedule_routes', function (Blueprint $table) {
            // Add back timing and pricing fields
            $table->datetime('departure_time')->nullable()->after('destination_airport_id');
            $table->datetime('arrival_time')->nullable()->after('departure_time');
            $table->decimal('distance_km', 8, 2)->nullable()->after('arrival_time');
            $table->decimal('segment_price', 10, 2)->nullable()->after('distance_km');
            $table->integer('duration_minutes')->nullable()->after('segment_price');
        });
    }
};
