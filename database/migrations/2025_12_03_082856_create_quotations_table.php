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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->date('quote_date');
            $table->foreignId('customer_id')->constrained('customers');

            // Flight/Route Information
            $table->string('departure_airport', 4)->nullable();
            $table->string('arrival_airport', 4)->nullable();
            $table->date('departure_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->date('return_date')->nullable();
            $table->time('return_time')->nullable();
            $table->integer('passengers')->nullable();
            $table->string('aircraft_type')->nullable();
            $table->text('route_description')->nullable();

            // Financial Information
            $table->foreignId('currency_id')->constrained('currencies');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);

            // Quote Management
            $table->foreignId('status_id')->constrained('quotation_statuses');
            $table->date('valid_until');
            $table->integer('version')->default(1);
            $table->foreignId('parent_quote_id')->nullable()->constrained('quotations'); // For revisions

            // Additional Information
            $table->text('terms_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional flight details

            // Tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['customer_id', 'status_id']);
            $table->index(['quote_date']);
            $table->index(['valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
