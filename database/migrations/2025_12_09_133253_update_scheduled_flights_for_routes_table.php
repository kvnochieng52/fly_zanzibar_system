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
            // Add flight route foreign key
            $table->foreignId('flight_route_id')->nullable()->constrained()->onDelete('cascade');

            // Keep the individual airport columns for backward compatibility for now
            // In production, you might want to migrate data first, then drop these columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_flights', function (Blueprint $table) {
            $table->dropForeign(['flight_route_id']);
            $table->dropColumn('flight_route_id');
        });
    }
};
