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
        Schema::create('aircraft_manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'name']);
        });

        // Seed initial data
        DB::table('aircraft_manufacturers')->insert([
            ['name' => 'Boeing', 'code' => 'BOEING', 'description' => 'Boeing Commercial Airplanes', 'country' => 'United States', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Airbus', 'code' => 'AIRBUS', 'description' => 'Airbus S.A.S.', 'country' => 'France', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bombardier', 'code' => 'BOMBARDIER', 'description' => 'Bombardier Aviation', 'country' => 'Canada', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Embraer', 'code' => 'EMBRAER', 'description' => 'Embraer S.A.', 'country' => 'Brazil', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ATR', 'code' => 'ATR', 'description' => 'ATR Aircraft', 'country' => 'France/Italy', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cessna', 'code' => 'CESSNA', 'description' => 'Cessna Aircraft Company', 'country' => 'United States', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beechcraft', 'code' => 'BEECHCRAFT', 'description' => 'Beechcraft Corporation', 'country' => 'United States', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Piper', 'code' => 'PIPER', 'description' => 'Piper Aircraft', 'country' => 'United States', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_manufacturers');
    }
};
