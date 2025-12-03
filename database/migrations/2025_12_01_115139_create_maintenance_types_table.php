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
        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('description')->nullable();
            $table->enum('category', ['scheduled', 'unscheduled', 'inspection', 'aog', 'modification']);
            $table->string('color', 7)->default('#007bff'); // Hex color for UI
            $table->boolean('requires_downtime')->default(true);
            $table->boolean('regulatory_required')->default(false);
            $table->integer('estimated_hours')->nullable(); // Default estimated hours
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default maintenance types
        DB::table('maintenance_types')->insert([
            [
                'name' => 'A-Check',
                'code' => 'A-CHECK',
                'description' => 'Light maintenance checks performed frequently',
                'category' => 'scheduled',
                'color' => '#28a745',
                'requires_downtime' => true,
                'regulatory_required' => true,
                'estimated_hours' => 20,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'B-Check',
                'code' => 'B-CHECK',
                'description' => 'Intermediate maintenance checks',
                'category' => 'scheduled',
                'color' => '#ffc107',
                'requires_downtime' => true,
                'regulatory_required' => true,
                'estimated_hours' => 60,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'C-Check',
                'code' => 'C-CHECK',
                'description' => 'Major maintenance checks',
                'category' => 'scheduled',
                'color' => '#fd7e14',
                'requires_downtime' => true,
                'regulatory_required' => true,
                'estimated_hours' => 200,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'D-Check',
                'code' => 'D-CHECK',
                'description' => 'Heavy maintenance checks',
                'category' => 'scheduled',
                'color' => '#dc3545',
                'requires_downtime' => true,
                'regulatory_required' => true,
                'estimated_hours' => 800,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Line Maintenance',
                'code' => 'LINE',
                'description' => 'Daily and transit checks',
                'category' => 'scheduled',
                'color' => '#17a2b8',
                'requires_downtime' => false,
                'regulatory_required' => true,
                'estimated_hours' => 2,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AOG Repair',
                'code' => 'AOG',
                'description' => 'Aircraft on Ground - Critical repairs',
                'category' => 'aog',
                'color' => '#dc3545',
                'requires_downtime' => true,
                'regulatory_required' => false,
                'estimated_hours' => 8,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unscheduled Repair',
                'code' => 'UNSCHED',
                'description' => 'Unplanned maintenance and repairs',
                'category' => 'unscheduled',
                'color' => '#6c757d',
                'requires_downtime' => true,
                'regulatory_required' => false,
                'estimated_hours' => 4,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Annual Inspection',
                'code' => 'ANNUAL',
                'description' => 'Annual airworthiness inspection',
                'category' => 'inspection',
                'color' => '#6f42c1',
                'requires_downtime' => true,
                'regulatory_required' => true,
                'estimated_hours' => 40,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_types');
    }
};
