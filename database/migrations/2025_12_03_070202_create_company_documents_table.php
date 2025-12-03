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
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_type_id')->constrained('company_document_types');
            $table->string('document_number');
            $table->string('title');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('version_revision')->nullable();
            $table->foreignId('issuing_authority_id')->constrained('issuing_authorities');
            $table->foreignId('responsible_department_id')->constrained('responsible_departments');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_size')->nullable();
            $table->string('file_type')->nullable();
            $table->foreignId('status_id')->constrained('company_document_statuses');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_current_version')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['document_type_id', 'expiry_date']);
            $table->index(['status_id']);
            $table->index(['responsible_department_id']);
            $table->unique(['document_type_id', 'document_number', 'version_revision'], 'company_docs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
