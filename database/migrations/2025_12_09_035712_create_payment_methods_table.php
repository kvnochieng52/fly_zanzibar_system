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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default payment methods
        DB::table('payment_methods')->insert([
            ['name' => 'Cash', 'code' => 'cash', 'description' => 'Cash payment', 'is_active' => true],
            ['name' => 'Bank Transfer', 'code' => 'bank_transfer', 'description' => 'Bank transfer payment', 'is_active' => true],
            ['name' => 'Credit/Debit Card', 'code' => 'card', 'description' => 'Credit or debit card payment', 'is_active' => true],
            ['name' => 'Cheque', 'code' => 'cheque', 'description' => 'Cheque payment', 'is_active' => true],
            ['name' => 'Mobile Money', 'code' => 'mobile_money', 'description' => 'Mobile money payment (M-Pesa, etc.)', 'is_active' => true],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
