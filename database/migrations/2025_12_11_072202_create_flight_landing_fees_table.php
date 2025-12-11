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
        Schema::create('flight_landing_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_flight_id')->constrained('scheduled_flights')->onDelete('cascade');
            $table->foreignId('airport_id')->constrained('airports')->onDelete('cascade');
            $table->decimal('mtow_used_kg', 10, 2); // Maximum Take-off Weight Used in kg
            $table->decimal('fee_amount', 10, 2); // Landing fee amount
            $table->string('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Index for performance
            $table->index(['scheduled_flight_id', 'airport_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_landing_fees');
    }
};
