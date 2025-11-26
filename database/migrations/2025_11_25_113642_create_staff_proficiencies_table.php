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
        Schema::create('staff_proficiencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->enum('proficiency_type', ['EPL', 'IR', 'OPC', 'LINE_CHECK', 'DG', 'SMS', 'CRM']);
            $table->string('certificate_number', 100)->nullable();

            // For EPL (English Proficiency Level)
            $table->integer('epl_level')->nullable(); // 1-6

            // For IR (Instrument Rating)
            $table->string('rating_type', 100)->nullable();
            $table->enum('renewal_status', ['current', 'due', 'overdue'])->nullable();

            // For OPC/Line Check
            $table->string('examiner_name', 200)->nullable();
            $table->string('check_type', 100)->nullable();
            $table->string('route_sector', 200)->nullable();
            $table->enum('check_status', ['pass', 'fail', 'pending'])->nullable();

            // Common fields
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('last_completion_date')->nullable();
            $table->date('next_due_date')->nullable();
            $table->string('training_provider', 200)->nullable();
            $table->string('testing_authority', 200)->nullable();
            $table->text('notes')->nullable();
            $table->string('document_file')->nullable();
            $table->enum('status', ['active', 'expired', 'pending', 'failed'])->default('active');
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'proficiency_type']);
            $table->index(['status', 'expiry_date']);
            $table->index(['next_due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_proficiencies');
    }
};
