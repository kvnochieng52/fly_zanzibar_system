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
        Schema::create('flight_cargo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_flight_id')->constrained()->onDelete('cascade');
            $table->string('cargo_type', 100); // general, medical, perishable, hazardous, etc.
            $table->text('description');
            $table->decimal('weight_kg', 10, 2); // weight in kilograms
            $table->decimal('volume_m3', 10, 3)->nullable(); // volume in cubic meters
            $table->string('dimensions')->nullable(); // length x width x height
            $table->decimal('declared_value', 12, 2)->nullable(); // declared value for insurance
            $table->string('currency', 3)->default('USD');
            $table->string('shipper_name')->nullable();
            $table->string('shipper_contact')->nullable();
            $table->string('consignee_name')->nullable();
            $table->string('consignee_contact')->nullable();
            $table->text('special_handling_instructions')->nullable();
            $table->boolean('requires_refrigeration')->default(false);
            $table->boolean('hazardous')->default(false);
            $table->string('tracking_number')->nullable();
            $table->enum('status', ['pending', 'loaded', 'in_transit', 'delivered', 'damaged'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['scheduled_flight_id', 'cargo_type']);
            $table->index('tracking_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_cargo');
    }
};
