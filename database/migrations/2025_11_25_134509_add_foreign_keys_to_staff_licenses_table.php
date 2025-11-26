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
        Schema::table('staff_licenses', function (Blueprint $table) {
            // Add the foreign key columns
            $table->foreignId('license_type_id')->constrained('license_types')->after('staff_id');
            $table->foreignId('license_status_id')->constrained('license_statuses')->after('document_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_licenses', function (Blueprint $table) {
            // Remove foreign key constraints and columns
            $table->dropForeign(['license_type_id']);
            $table->dropForeign(['license_status_id']);
            $table->dropColumn(['license_type_id', 'license_status_id']);
        });
    }
};
