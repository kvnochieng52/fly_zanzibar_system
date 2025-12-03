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
        Schema::create('aircraft_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircraft_id')->constrained('aircraft')->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained('aircraft_document_types');

            // Common fields for all document types
            $table->string('document_number')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->string('file_path')->nullable(); // Path to uploaded document
            $table->boolean('is_current')->default(true);

            // Specific fields for different document types (JSON for flexibility)
            $table->json('document_details')->nullable(); // Store type-specific fields

            $table->foreignId('document_status_id')->constrained('aircraft_document_statuses');

            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['aircraft_id', 'document_type_id']);
            $table->index(['expiry_date']);
            $table->index(['document_status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_documents');
    }
};
