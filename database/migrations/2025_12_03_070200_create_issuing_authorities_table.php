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
        Schema::create('issuing_authorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('country')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default issuing authorities
        DB::table('issuing_authorities')->insert([
            ['name' => 'Civil Aviation Authority', 'code' => 'CAA', 'description' => 'Civil Aviation Authority', 'country' => 'Tanzania'],
            ['name' => 'International Civil Aviation Organization', 'code' => 'ICAO', 'description' => 'International Civil Aviation Organization', 'country' => 'International'],
            ['name' => 'Federal Aviation Administration', 'code' => 'FAA', 'description' => 'Federal Aviation Administration', 'country' => 'USA'],
            ['name' => 'European Aviation Safety Agency', 'code' => 'EASA', 'description' => 'European Aviation Safety Agency', 'country' => 'Europe'],
            ['name' => 'Insurance Company', 'code' => 'INS', 'description' => 'Insurance Provider', 'country' => null],
            ['name' => 'Leasing Company', 'code' => 'LEASE', 'description' => 'Aircraft Leasing Company', 'country' => null],
            ['name' => 'Manufacturer', 'code' => 'MFG', 'description' => 'Aircraft/Equipment Manufacturer', 'country' => null],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuing_authorities');
    }
};
