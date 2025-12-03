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
        Schema::table('aircraft', function (Blueprint $table) {
            // Add new foreign key columns
            $table->foreignId('manufacturer_id')->nullable()->after('registration_number')->constrained('aircraft_manufacturers');
            $table->foreignId('model_id')->nullable()->after('manufacturer_id')->constrained('aircraft_models');

            // Drop the old columns after adding the foreign keys
            $table->dropColumn(['manufacturer', 'model_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aircraft', function (Blueprint $table) {
            // Add back the old columns
            $table->string('manufacturer')->after('registration_number');
            $table->string('model_type')->after('manufacturer');

            // Drop foreign key columns
            $table->dropForeign(['manufacturer_id']);
            $table->dropForeign(['model_id']);
            $table->dropColumn(['manufacturer_id', 'model_id']);
        });
    }
};
