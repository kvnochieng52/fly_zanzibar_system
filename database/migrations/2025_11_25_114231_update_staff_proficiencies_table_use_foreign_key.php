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
        Schema::table('staff_proficiencies', function (Blueprint $table) {
            // Add the foreign key for proficiency type
            $table->foreignId('proficiency_type_id')->after('staff_id')->constrained('proficiency_types')->onDelete('cascade');

            // Remove the old enum column
            $table->dropColumn('proficiency_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_proficiencies', function (Blueprint $table) {
            // Add back the enum column
            $table->enum('proficiency_type', ['EPL', 'IR', 'OPC', 'LINE_CHECK', 'DG', 'SMS', 'CRM'])->after('staff_id');

            // Remove the foreign key
            $table->dropForeign(['proficiency_type_id']);
            $table->dropColumn('proficiency_type_id');
        });
    }
};
