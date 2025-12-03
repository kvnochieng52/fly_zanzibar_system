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
        Schema::create('priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('description')->nullable();
            $table->string('color', 7)->default('#6c757d'); // Hex color for UI
            $table->integer('level')->unique(); // Numeric level for sorting (1=lowest, 5=highest)
            $table->enum('context', ['maintenance', 'work_order', 'general'])->default('general'); // Where this priority applies
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default priorities
        DB::table('priorities')->insert([
            [
                'name' => 'Low',
                'code' => 'LOW',
                'description' => 'Low priority - can be scheduled flexibly',
                'color' => '#28a745',
                'level' => 1,
                'context' => 'general',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Normal',
                'code' => 'NORMAL',
                'description' => 'Normal priority - standard scheduling',
                'color' => '#17a2b8',
                'level' => 2,
                'context' => 'general',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'High',
                'code' => 'HIGH',
                'description' => 'High priority - schedule soon',
                'color' => '#ffc107',
                'level' => 3,
                'context' => 'general',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Critical',
                'code' => 'CRITICAL',
                'description' => 'Critical priority - immediate attention required',
                'color' => '#dc3545',
                'level' => 4,
                'context' => 'general',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'AOG',
                'code' => 'AOG',
                'description' => 'Aircraft on Ground - highest priority',
                'color' => '#6f42c1',
                'level' => 5,
                'context' => 'maintenance',
                'sort_order' => 5,
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
        Schema::dropIfExists('priorities');
    }
};
