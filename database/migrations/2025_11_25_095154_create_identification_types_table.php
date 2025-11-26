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
        Schema::create('identification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'name']);
        });

        // Insert default identification types
        DB::table('identification_types')->insert([
            [
                'name' => 'National ID',
                'code' => 'NATIONAL_ID',
                'description' => 'National Identity Card',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Passport',
                'code' => 'PASSPORT',
                'description' => 'International Passport',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Driving License',
                'code' => 'DRIVING_LICENSE',
                'description' => 'Driver\'s License',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Work Permit',
                'code' => 'WORK_PERMIT',
                'description' => 'Work Permit/Visa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Birth Certificate',
                'code' => 'BIRTH_CERT',
                'description' => 'Birth Certificate',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Military ID',
                'code' => 'MILITARY_ID',
                'description' => 'Military Identification',
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
        Schema::dropIfExists('identification_types');
    }
};
