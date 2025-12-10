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
            // Add primary customer field
            $table->foreignId('primary_customer_id')->nullable()->after('flight_type')->constrained('customers')->onDelete('set null');

            // Remove capacity and available_seats fields
            $table->dropColumn(['capacity', 'available_seats']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_flights', function (Blueprint $table) {
            // Remove primary customer field
            $table->dropForeign(['primary_customer_id']);
            $table->dropColumn('primary_customer_id');

            // Add back capacity and available_seats fields
            $table->integer('capacity')->nullable()->after('flight_type');
            $table->integer('available_seats')->nullable()->after('capacity');
        });
    }
};
