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
        // Update the status ENUM to include 'expiring_soon'
        DB::statement("ALTER TABLE staff_type_ratings MODIFY COLUMN status ENUM('active', 'expired', 'suspended', 'revoked', 'expiring_soon') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE staff_type_ratings MODIFY COLUMN status ENUM('active', 'expired', 'suspended', 'revoked') DEFAULT 'active'");
    }
};
