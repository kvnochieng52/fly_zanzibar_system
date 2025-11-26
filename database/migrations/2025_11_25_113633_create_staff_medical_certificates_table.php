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
        Schema::create('staff_medical_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->enum('medical_class', ['Class 1', 'Class 2', 'Class 3']);
            $table->string('certificate_number', 100);
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->string('examining_doctor', 200);
            $table->string('examining_facility', 200);
            $table->enum('medical_status', ['valid', 'expired', 'suspended', 'revoked'])->default('valid');
            $table->text('limitations')->nullable();
            $table->text('notes')->nullable();
            $table->string('document_file')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'medical_class']);
            $table->index(['medical_status', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_medical_certificates');
    }
};
