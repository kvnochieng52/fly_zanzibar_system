<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotation_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('color', 7)->default('#6c757d'); // Hex color for UI
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default statuses
        DB::table('quotation_statuses')->insert([
            ['name' => 'Draft', 'code' => 'DRAFT', 'color' => '#6c757d', 'description' => 'Quote is being prepared', 'is_default' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sent', 'code' => 'SENT', 'color' => '#17a2b8', 'description' => 'Quote has been sent to customer', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Accepted', 'code' => 'ACCEPTED', 'color' => '#28a745', 'description' => 'Quote has been accepted by customer', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rejected', 'code' => 'REJECTED', 'color' => '#dc3545', 'description' => 'Quote has been rejected by customer', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Expired', 'code' => 'EXPIRED', 'color' => '#6f42c1', 'description' => 'Quote has passed its validity period', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_statuses');
    }
};
