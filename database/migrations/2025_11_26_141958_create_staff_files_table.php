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
        Schema::create('staff_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // mime type
            $table->unsignedBigInteger('file_size')->nullable(); // size in bytes
            $table->text('description')->nullable();
            $table->string('category')->default('general'); // general, contract, certificate, etc.
            $table->string('uploaded_by')->nullable(); // who uploaded the file
            $table->boolean('is_public')->default(false); // whether file is publicly accessible
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'category']);
            $table->index(['staff_id', 'is_active']);
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_files');
    }
};
