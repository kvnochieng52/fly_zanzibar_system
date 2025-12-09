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
        Schema::create('landing_fees', function (Blueprint $table) {
            $table->id();
            $table->string('flight_number');
            $table->date('flight_date');
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->foreignId('airport_id')->constrained('airports')->onDelete('cascade');
            $table->decimal('mtow_used', 8, 2); // MTOW used for this flight
            $table->decimal('fee_amount', 10, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->foreignId('payment_status_id')->constrained('payment_statuses');
            $table->date('payment_date')->nullable();
            $table->string('receipt_reference')->nullable();
            $table->string('invoice_reference')->nullable();
            $table->text('authority_document')->nullable(); // File path or reference
            $table->text('notes')->nullable();
            $table->boolean('is_calculated_auto')->default(false); // Was fee calculated automatically?
            $table->decimal('manual_override_amount', 10, 2)->nullable(); // If manually overridden
            $table->string('override_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes for better performance
            $table->index(['flight_date', 'aircraft_id']);
            $table->index(['airport_id', 'flight_date']);
            $table->index(['payment_status_id']);
            $table->unique(['flight_number', 'flight_date', 'airport_id']); // Prevent duplicates
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_fees');
    }
};
