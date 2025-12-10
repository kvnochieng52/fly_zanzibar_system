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
        Schema::table('scheduled_flights', function (Blueprint $table) {
            $table->enum('flight_type', ['passenger', 'cargo', 'mixed'])->default('passenger')->after('flight_status_id');
            $table->integer('passenger_count')->default(0)->after('available_seats');
            $table->decimal('total_cargo_weight_kg', 10, 2)->default(0)->after('passenger_count');
            $table->integer('cargo_items_count')->default(0)->after('total_cargo_weight_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_flights', function (Blueprint $table) {
            $table->dropColumn(['flight_type', 'passenger_count', 'total_cargo_weight_kg', 'cargo_items_count']);
        });
    }
};
