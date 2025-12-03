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
        Schema::create('aircraft_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->foreignId('maintenance_type_id')->constrained('maintenance_types');
            $table->string('schedule_name');
            $table->text('description')->nullable();

            // Due by criteria (at least one must be set)
            $table->integer('due_hours')->nullable(); // Flight hours
            $table->integer('due_cycles')->nullable(); // Flight cycles
            $table->date('due_calendar_date')->nullable(); // Calendar date

            // Current status
            $table->integer('current_hours')->default(0);
            $table->integer('current_cycles')->default(0);

            // Last maintenance
            $table->date('last_completed_date')->nullable();
            $table->integer('last_completed_hours')->nullable();
            $table->integer('last_completed_cycles')->nullable();

            // Next due calculations (computed fields)
            $table->date('next_due_date')->nullable();
            $table->integer('next_due_hours')->nullable();
            $table->integer('next_due_cycles')->nullable();

            // Intervals for recurring maintenance
            $table->integer('interval_hours')->nullable();
            $table->integer('interval_cycles')->nullable();
            $table->integer('interval_days')->nullable();

            // Status and priority
            $table->enum('compliance_status', ['current', 'due_soon', 'overdue'])->default('current');
            $table->enum('priority', ['low', 'normal', 'high', 'critical'])->default('normal');
            $table->boolean('regulatory_required')->default(false);
            $table->boolean('is_active')->default(true);

            // Tolerance settings
            $table->integer('warning_hours')->default(10); // Hours before due
            $table->integer('warning_cycles')->default(5); // Cycles before due
            $table->integer('warning_days')->default(7); // Days before due

            $table->timestamps();

            // Indexes
            $table->index(['aircraft_id', 'compliance_status'], 'ams_aircraft_compliance_idx');
            $table->index(['next_due_date'], 'ams_next_due_date_idx');
            $table->index(['maintenance_type_id'], 'ams_maintenance_type_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_maintenance_schedules');
    }
};
