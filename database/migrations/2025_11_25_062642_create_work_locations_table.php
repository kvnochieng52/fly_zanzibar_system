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
        Schema::create('work_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 10)->unique();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('type', 50)->default('Office'); // Airport, Office, Branch, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('name');
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_locations');
    }
};
