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
        Schema::create('aircraft', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('manufacturer');
            $table->string('model_type');
            $table->string('serial_number')->unique();
            $table->year('year_of_manufacture');
            $table->decimal('total_airframe_hours', 10, 2)->default(0);
            $table->integer('total_cycles')->default(0);
            $table->foreignId('status_id')->constrained('aircraft_statuses');
            $table->string('home_base');
            $table->text('seating_configuration')->nullable();
            $table->json('photos')->nullable(); // Array of photo paths
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft');
    }
};
