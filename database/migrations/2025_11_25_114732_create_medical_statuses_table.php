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
        Schema::create('medical_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 20)->unique();
            $table->string('color', 20)->default('secondary');
            $table->text('description')->nullable();
            $table->boolean('is_active_status')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'code']);
        });

        // Insert default medical statuses
        DB::table('medical_statuses')->insert([
            [
                'name' => 'Valid',
                'code' => 'valid',
                'color' => 'success',
                'description' => 'Medical certificate is valid',
                'is_active_status' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Expired',
                'code' => 'expired',
                'color' => 'danger',
                'description' => 'Medical certificate has expired',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suspended',
                'code' => 'suspended',
                'color' => 'warning',
                'description' => 'Medical certificate is temporarily suspended',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Revoked',
                'code' => 'revoked',
                'color' => 'dark',
                'description' => 'Medical certificate has been permanently revoked',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Under Review',
                'code' => 'under_review',
                'color' => 'warning',
                'description' => 'Medical certificate is under medical review',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Restricted',
                'code' => 'restricted',
                'color' => 'warning',
                'description' => 'Medical certificate has restrictions or limitations',
                'is_active_status' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_statuses');
    }
};
