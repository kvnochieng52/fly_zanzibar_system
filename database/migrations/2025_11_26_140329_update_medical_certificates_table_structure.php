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
        // Check if the table has the foreign key columns already
        if (!Schema::hasColumn('staff_medical_certificates', 'medical_class_id')) {
            Schema::table('staff_medical_certificates', function (Blueprint $table) {
                // Add foreign key columns
                $table->foreignId('medical_class_id')->nullable()->after('staff_id')->constrained('medical_classes')->onDelete('cascade');
                $table->foreignId('medical_status_id')->nullable()->after('medical_class_id')->constrained('medical_statuses')->onDelete('cascade');
            });
        }

        // Migrate data from ENUM to foreign keys if ENUM columns exist
        if (Schema::hasColumn('staff_medical_certificates', 'medical_class')) {
            // Migrate medical_class data
            DB::statement("
                UPDATE staff_medical_certificates
                SET medical_class_id = (
                    SELECT id FROM medical_classes
                    WHERE LOWER(name) LIKE CONCAT('%', LOWER(SUBSTRING(medical_class, 7)), '%')
                    LIMIT 1
                )
                WHERE medical_class_id IS NULL AND medical_class IS NOT NULL
            ");

            // Migrate medical_status data
            DB::statement("
                UPDATE staff_medical_certificates
                SET medical_status_id = (
                    SELECT id FROM medical_statuses
                    WHERE LOWER(name) = LOWER(medical_status)
                    LIMIT 1
                )
                WHERE medical_status_id IS NULL AND medical_status IS NOT NULL
            ");

            // Remove old ENUM columns
            Schema::table('staff_medical_certificates', function (Blueprint $table) {
                $table->dropColumn(['medical_class', 'medical_status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_medical_certificates', function (Blueprint $table) {
            // Add back ENUM columns
            $table->enum('medical_class', ['Class 1', 'Class 2', 'Class 3'])->nullable();
            $table->enum('medical_status', ['valid', 'expired', 'suspended', 'revoked'])->default('valid');

            // Drop foreign key columns
            $table->dropForeign(['medical_class_id']);
            $table->dropForeign(['medical_status_id']);
            $table->dropColumn(['medical_class_id', 'medical_status_id']);
        });
    }
};
