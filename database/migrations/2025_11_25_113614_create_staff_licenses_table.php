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
        Schema::create('staff_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->enum('license_type', ['CPL', 'ATPL', 'PPL', 'IR', 'MEP', 'SEP', 'RNAV', 'Other']);
            $table->string('license_number', 100);
            $table->string('issuing_authority', 150);
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->text('restrictions')->nullable();
            $table->text('notes')->nullable();
            $table->string('document_file')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'revoked'])->default('active');
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'license_type']);
            $table->index(['status', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_licenses');
    }
};
