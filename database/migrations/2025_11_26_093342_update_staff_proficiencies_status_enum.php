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
        // Update the status ENUM to include 'due_soon' and 'overdue'
        DB::statement("ALTER TABLE staff_proficiencies MODIFY COLUMN status ENUM('active', 'expired', 'pending', 'failed', 'due_soon', 'overdue') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE staff_proficiencies MODIFY COLUMN status ENUM('active', 'expired', 'pending', 'failed') DEFAULT 'active'");
    }
};
