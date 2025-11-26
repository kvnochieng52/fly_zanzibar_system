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
        Schema::create('license_statuses', function (Blueprint $table) {
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

        // Insert default license statuses
        DB::table('license_statuses')->insert([
            [
                'name' => 'Active',
                'code' => 'active',
                'color' => 'success',
                'description' => 'License is active and valid',
                'is_active_status' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Expired',
                'code' => 'expired',
                'color' => 'danger',
                'description' => 'License has expired',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suspended',
                'code' => 'suspended',
                'color' => 'warning',
                'description' => 'License is temporarily suspended',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Revoked',
                'code' => 'revoked',
                'color' => 'dark',
                'description' => 'License has been permanently revoked',
                'is_active_status' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pending Renewal',
                'code' => 'pending_renewal',
                'color' => 'warning',
                'description' => 'License renewal is in progress',
                'is_active_status' => false,
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
        Schema::dropIfExists('license_statuses');
    }
};
