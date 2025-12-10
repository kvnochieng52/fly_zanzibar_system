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
        Schema::table('flight_passengers', function (Blueprint $table) {
            // Make first_name and last_name nullable for backward compatibility
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();

            // Drop the virtual full_name column first
            $table->dropColumn('full_name');
        });

        // Re-add the virtual full_name column with updated logic
        Schema::table('flight_passengers', function (Blueprint $table) {
            $table->string('full_name')->virtualAs("
                CASE
                    WHEN customer_name IS NOT NULL AND customer_name != '' THEN customer_name
                    ELSE concat(COALESCE(first_name, ''), ' ', COALESCE(last_name, ''))
                END
            ")->after('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flight_passengers', function (Blueprint $table) {
            // Drop the virtual full_name column
            $table->dropColumn('full_name');

            // Make first_name and last_name required again
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });

        // Re-add the original virtual full_name column
        Schema::table('flight_passengers', function (Blueprint $table) {
            $table->string('full_name')->virtualAs("concat(first_name, ' ', last_name)")->after('last_name');
        });
    }
};
