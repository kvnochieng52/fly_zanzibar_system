<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('aircraft_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturer_id')->constrained('aircraft_manufacturers')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('Commercial'); // Commercial, General Aviation, etc.
            $table->integer('typical_seating')->nullable();
            $table->string('engine_type')->nullable(); // Turbofan, Turboprop, Piston
            $table->json('specifications')->nullable(); // Technical specs as JSON
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['manufacturer_id', 'name']);
            $table->index(['manufacturer_id', 'is_active']);
        });

        // Seed initial data
        DB::table('aircraft_models')->insert([
            // Boeing Models
            ['manufacturer_id' => 1, 'name' => '737-800', 'code' => 'B738', 'description' => 'Boeing 737-800', 'category' => 'Commercial', 'typical_seating' => 162, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['manufacturer_id' => 1, 'name' => '777-300ER', 'code' => 'B77W', 'description' => 'Boeing 777-300ER', 'category' => 'Commercial', 'typical_seating' => 396, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['manufacturer_id' => 1, 'name' => '787-8', 'code' => 'B788', 'description' => 'Boeing 787-8 Dreamliner', 'category' => 'Commercial', 'typical_seating' => 242, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Airbus Models
            ['manufacturer_id' => 2, 'name' => 'A320', 'code' => 'A320', 'description' => 'Airbus A320', 'category' => 'Commercial', 'typical_seating' => 150, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['manufacturer_id' => 2, 'name' => 'A350-900', 'code' => 'A359', 'description' => 'Airbus A350-900', 'category' => 'Commercial', 'typical_seating' => 315, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['manufacturer_id' => 2, 'name' => 'A380', 'code' => 'A380', 'description' => 'Airbus A380', 'category' => 'Commercial', 'typical_seating' => 525, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],

            // ATR Models
            ['manufacturer_id' => 5, 'name' => 'ATR 42-600', 'code' => 'AT46', 'description' => 'ATR 42-600', 'category' => 'Regional', 'typical_seating' => 48, 'engine_type' => 'Turboprop', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['manufacturer_id' => 5, 'name' => 'ATR 72-600', 'code' => 'AT76', 'description' => 'ATR 72-600', 'category' => 'Regional', 'typical_seating' => 68, 'engine_type' => 'Turboprop', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],

            // Cessna Models
            ['manufacturer_id' => 6, 'name' => 'Citation CJ4', 'code' => 'C25C', 'description' => 'Cessna Citation CJ4', 'category' => 'Business Jet', 'typical_seating' => 9, 'engine_type' => 'Turbofan', 'is_active' => true, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['manufacturer_id' => 6, 'name' => '172', 'code' => 'C172', 'description' => 'Cessna 172 Skyhawk', 'category' => 'General Aviation', 'typical_seating' => 4, 'engine_type' => 'Piston', 'is_active' => true, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_models');
    }
};
