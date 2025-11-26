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
        Schema::create('staff_type_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_license_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('aircraft_type', 100); // e.g., A320, B737, DHC-6
            $table->string('rating_type', 50); // e.g., PIC, SIC, PF, PNF
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('issuing_authority', 150);
            $table->text('limitations')->nullable();
            $table->string('document_file')->nullable();
            $table->enum('status', ['active', 'expired', 'suspended', 'revoked'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index(['staff_id', 'aircraft_type']);
            $table->index(['status', 'expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_type_ratings');
    }
};
