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
        Schema::create('responsible_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default responsible departments
        DB::table('responsible_departments')->insert([
            ['name' => 'Quality Assurance', 'code' => 'QA', 'description' => 'Quality Assurance Department'],
            ['name' => 'Flight Operations', 'code' => 'FLOPS', 'description' => 'Flight Operations Department'],
            ['name' => 'Maintenance', 'code' => 'MAINT', 'description' => 'Maintenance Department'],
            ['name' => 'Legal & Compliance', 'code' => 'LEGAL', 'description' => 'Legal and Compliance Department'],
            ['name' => 'Safety', 'code' => 'SAFETY', 'description' => 'Safety Department'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human Resources Department'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance Department'],
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Administration Department'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsible_departments');
    }
};
