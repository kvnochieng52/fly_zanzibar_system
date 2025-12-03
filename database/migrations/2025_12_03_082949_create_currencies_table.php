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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // USD, EUR, etc.
            $table->string('name'); // US Dollar, Euro, etc.
            $table->string('symbol', 5); // $, €, etc.
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // Rate against base currency
            $table->boolean('is_base')->default(false); // Only one base currency
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default currencies
        DB::table('currencies')->insert([
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.0000, 'is_base' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.8500, 'is_base' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.7300, 'is_base' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'TZS', 'name' => 'Tanzanian Shilling', 'symbol' => 'TSh', 'exchange_rate' => 2500.0000, 'is_base' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
