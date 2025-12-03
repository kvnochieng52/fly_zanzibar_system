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
        Schema::create('work_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('description')->nullable();
            $table->string('color', 7)->default('#007bff');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_final_status')->default(false); // Completed, Cancelled
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default statuses
        DB::table('work_order_statuses')->insert([
            [
                'name' => 'Open',
                'code' => 'OPEN',
                'description' => 'Work order created and waiting to be started',
                'color' => '#17a2b8',
                'is_final_status' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'In Progress',
                'code' => 'IN_PROGRESS',
                'description' => 'Work is currently being performed',
                'color' => '#ffc107',
                'is_final_status' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'On Hold',
                'code' => 'ON_HOLD',
                'description' => 'Work paused waiting for parts or approval',
                'color' => '#fd7e14',
                'is_final_status' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quality Check',
                'code' => 'QC',
                'description' => 'Work completed, undergoing quality inspection',
                'color' => '#6f42c1',
                'is_final_status' => false,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Completed',
                'code' => 'COMPLETED',
                'description' => 'Work order successfully completed',
                'color' => '#28a745',
                'is_final_status' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cancelled',
                'code' => 'CANCELLED',
                'description' => 'Work order cancelled',
                'color' => '#6c757d',
                'is_final_status' => true,
                'sort_order' => 6,
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
        Schema::dropIfExists('work_order_statuses');
    }
};
