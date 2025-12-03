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
        Schema::create('work_order_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');

            // Part identification
            $table->string('part_number');
            $table->string('part_name');
            $table->text('part_description')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('batch_lot_number')->nullable();

            // Quantities
            $table->integer('quantity_required');
            $table->integer('quantity_used')->default(0);
            $table->integer('quantity_removed')->default(0);

            // Part condition and status
            $table->enum('condition', ['new', 'serviceable', 'repairable', 'condemned', 'unknown'])->default('new');
            $table->enum('status', ['requested', 'reserved', 'issued', 'installed', 'returned'])->default('requested');

            // Cost tracking
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);

            // Installation details
            $table->string('installation_position')->nullable();
            $table->datetime('installed_at')->nullable();
            $table->datetime('removed_at')->nullable();

            // Part life tracking
            $table->integer('part_total_hours')->nullable();
            $table->integer('part_total_cycles')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();

            // Vendor and sourcing
            $table->string('vendor')->nullable();
            $table->string('purchase_order_number')->nullable();
            $table->date('delivery_date')->nullable();

            // Compliance and certification
            $table->string('certification_reference')->nullable();
            $table->boolean('approved_part')->default(true);
            $table->text('compliance_notes')->nullable();

            // Additional tracking
            $table->text('notes')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->foreignId('installed_by')->nullable()->constrained('users');

            $table->timestamps();

            // Indexes
            $table->index(['work_order_id', 'status'], 'wop_order_status_idx');
            $table->index(['part_number'], 'wop_part_number_idx');
            $table->index(['serial_number'], 'wop_serial_number_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_parts');
    }
};
