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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_number')->unique();
            $table->foreignId('aircraft_id')->constrained('aircraft');
            $table->foreignId('maintenance_type_id')->constrained('maintenance_types');
            $table->foreignId('maintenance_organization_id')->constrained('maintenance_organizations');
            $table->foreignId('work_order_status_id')->constrained('work_order_statuses');
            $table->foreignId('maintenance_schedule_id')->nullable()->constrained('aircraft_maintenance_schedules');

            // Work order details
            $table->string('title');
            $table->text('description');
            $table->text('work_scope')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal');

            // Scheduling
            $table->datetime('scheduled_start')->nullable();
            $table->datetime('scheduled_completion')->nullable();
            $table->datetime('actual_start')->nullable();
            $table->datetime('actual_completion')->nullable();

            // Aircraft condition when work started
            $table->integer('aircraft_hours_at_start')->nullable();
            $table->integer('aircraft_cycles_at_start')->nullable();

            // Cost tracking
            $table->decimal('estimated_cost', 12, 2)->default(0);
            $table->decimal('actual_cost', 12, 2)->default(0);
            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('parts_cost', 12, 2)->default(0);
            $table->decimal('external_cost', 12, 2)->default(0);

            // Time tracking
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('actual_hours', 8, 2)->nullable();

            // Downtime tracking
            $table->datetime('aircraft_grounded_at')->nullable();
            $table->datetime('aircraft_released_at')->nullable();
            $table->integer('downtime_hours')->nullable(); // Calculated field

            // Quality and compliance
            $table->boolean('quality_check_required')->default(false);
            $table->boolean('quality_check_passed')->nullable();
            $table->datetime('quality_check_date')->nullable();
            $table->text('quality_notes')->nullable();

            // Regulatory compliance
            $table->boolean('regulatory_sign_off_required')->default(false);
            $table->boolean('regulatory_sign_off_completed')->nullable();
            $table->string('regulatory_reference')->nullable();

            // Additional fields
            $table->text('notes')->nullable();
            $table->text('completion_notes')->nullable();
            $table->json('custom_fields')->nullable();

            // System fields
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['aircraft_id', 'work_order_status_id'], 'wo_aircraft_status_idx');
            $table->index(['scheduled_start', 'scheduled_completion'], 'wo_schedule_idx');
            $table->index(['maintenance_type_id', 'priority'], 'wo_type_priority_idx');
            $table->index(['work_order_number'], 'wo_number_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
