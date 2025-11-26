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
        Schema::create('medical_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->integer('validity_period_months');
            $table->json('privileges')->nullable();
            $table->json('limitations')->nullable();
            $table->boolean('requires_renewal')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'code']);
        });

        // Insert default medical classes
        DB::table('medical_classes')->insert([
            [
                'name' => 'Class 1 Medical',
                'code' => 'CLASS_1',
                'description' => 'First Class Medical Certificate for airline transport pilots and commercial pilots over 40',
                'validity_period_months' => 6, // 6 months for pilots over 40, 12 months under 40
                'privileges' => json_encode([
                    'ATPL privileges',
                    'CPL privileges',
                    'PPL privileges',
                    'Flight instructor privileges'
                ]),
                'limitations' => json_encode([
                    'Age-based validity periods',
                    'Regular renewal required'
                ]),
                'requires_renewal' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Class 2 Medical',
                'code' => 'CLASS_2',
                'description' => 'Second Class Medical Certificate for commercial pilots under 40',
                'validity_period_months' => 12,
                'privileges' => json_encode([
                    'CPL privileges',
                    'PPL privileges',
                    'Flight instructor privileges'
                ]),
                'limitations' => json_encode([
                    'No ATPL privileges',
                    'Age restrictions apply'
                ]),
                'requires_renewal' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Class 3 Medical',
                'code' => 'CLASS_3',
                'description' => 'Third Class Medical Certificate for private pilots',
                'validity_period_months' => 24, // 24 months under 40, 12 months over 40
                'privileges' => json_encode([
                    'PPL privileges only',
                    'Student pilot privileges'
                ]),
                'limitations' => json_encode([
                    'No commercial privileges',
                    'Limited to private operations'
                ]),
                'requires_renewal' => true,
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
        Schema::dropIfExists('medical_classes');
    }
};
