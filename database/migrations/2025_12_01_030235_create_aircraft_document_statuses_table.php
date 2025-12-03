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
        Schema::create('aircraft_document_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6c757d'); // Hex color code
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default document statuses
        DB::table('aircraft_document_statuses')->insert([
            [
                'name' => 'Valid',
                'code' => 'VALID',
                'description' => 'Document is currently valid',
                'color' => '#28a745',
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Expiring Soon',
                'code' => 'EXPIRING',
                'description' => 'Document expires within 30 days',
                'color' => '#ffc107',
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Expired',
                'code' => 'EXPIRED',
                'description' => 'Document has expired',
                'color' => '#dc3545',
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Pending Review',
                'code' => 'PENDING',
                'description' => 'Document is pending review',
                'color' => '#007bff',
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aircraft_document_statuses');
    }
};
