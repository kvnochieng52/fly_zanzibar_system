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
        Schema::create('employment_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('contract_file')->nullable(); // Path to the contract file
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Nullable for permanent contracts
            $table->enum('contract_type', ['fixed_term', 'permanent', 'probation', 'temporary'])->default('fixed_term');
            $table->enum('status', ['active', 'expired', 'terminated', 'pending'])->default('pending');
            $table->text('notes')->nullable();
            $table->boolean('is_current')->default(false); // Mark the current active contract
            $table->string('uploaded_by')->nullable(); // User who uploaded the contract
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'is_current']);
            $table->index(['status', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_contracts');
    }
};
