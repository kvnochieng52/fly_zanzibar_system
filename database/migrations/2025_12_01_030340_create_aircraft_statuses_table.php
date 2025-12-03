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
        Schema::create('aircraft_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6c757d'); // Hex color code
            $table->boolean('allows_operation')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default aircraft statuses
        DB::table('aircraft_statuses')->insert([
            [
                'name' => 'In Service',
                'code' => 'IN_SERVICE',
                'description' => 'Aircraft is actively in service and operational',
                'color' => '#28a745',
                'allows_operation' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Maintenance',
                'code' => 'MAINTENANCE',
                'description' => 'Aircraft is undergoing scheduled or unscheduled maintenance',
                'color' => '#ffc107',
                'allows_operation' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'AOG',
                'code' => 'AOG',
                'description' => 'Aircraft on Ground - not operational due to mechanical issues',
                'color' => '#dc3545',
                'allows_operation' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Retired',
                'code' => 'RETIRED',
                'description' => 'Aircraft has been retired from active service',
                'color' => '#6c757d',
                'allows_operation' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_statuses');
    }
};
