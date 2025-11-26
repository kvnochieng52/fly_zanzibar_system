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
        Schema::table('staff', function (Blueprint $table) {
            // Add the new foreign key column
            $table->foreignId('identification_type_id')->nullable()->after('identification_type')->constrained('identification_types');

            // Keep the old identification_type column for now (we'll remove it later if needed)
            // $table->dropColumn('identification_type'); // Uncomment this line to remove the old column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['identification_type_id']);
            $table->dropColumn('identification_type_id');
        });
    }
};
