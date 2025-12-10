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
        Schema::create('flight_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheduled_flight_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name')->virtualAs("concat(first_name, ' ', last_name)");
            $table->string('id_number')->nullable();
            $table->enum('id_type', ['passport', 'national_id', 'drivers_license', 'other'])->default('passport');
            $table->string('nationality', 100);
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('special_requirements')->nullable(); // dietary, medical, etc.
            $table->enum('passenger_type', ['adult', 'child', 'infant'])->default('adult');
            $table->string('seat_preference', 50)->nullable(); // window, aisle, middle
            $table->boolean('checked_in')->default(false);
            $table->timestamp('check_in_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['scheduled_flight_id', 'last_name']);
            $table->index(['id_number', 'id_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_passengers');
    }
};
