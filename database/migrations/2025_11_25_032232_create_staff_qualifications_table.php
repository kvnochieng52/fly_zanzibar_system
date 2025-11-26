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
        Schema::create('staff_qualifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('qualification_type', 100); // Certificate, License, Training, etc.
            $table->string('qualification_name', 200);
            $table->string('issuing_authority', 200)->nullable();
            $table->string('certificate_number', 100)->nullable();
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('qualification_document')->nullable(); // File path for stored document
            $table->enum('status', ['valid', 'expired', 'suspended', 'revoked'])->default('valid');
            $table->text('notes')->nullable();
            $table->boolean('is_mandatory')->default(false); // Required for the position
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->index(['staff_id', 'status']);
            $table->index(['expiry_date', 'status']); // For expiry notifications
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_qualifications');
    }
};
