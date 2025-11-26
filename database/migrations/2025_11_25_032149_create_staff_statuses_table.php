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
        Schema::create('staff_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#007bff'); // For UI display (hex color)
            $table->boolean('is_active')->default(true);
            $table->boolean('allows_access')->default(true); // Whether this status allows system access
            $table->timestamps();

            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_statuses');
    }
};
