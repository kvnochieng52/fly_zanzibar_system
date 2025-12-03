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
        Schema::create('maintenance_organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('description')->nullable();
            $table->enum('type', ['internal', 'external']);
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('certification_number')->nullable(); // FAA/EASA certification
            $table->date('certification_expiry')->nullable();
            $table->json('capabilities')->nullable(); // What types of maintenance they can perform
            $table->decimal('hourly_rate_usd', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default organizations
        DB::table('maintenance_organizations')->insert([
            [
                'name' => 'In-House Maintenance',
                'code' => 'INTERNAL',
                'description' => 'Internal maintenance department',
                'type' => 'internal',
                'contact_person' => 'Maintenance Manager',
                'email' => null,
                'phone' => null,
                'address' => null,
                'certification_number' => null,
                'certification_expiry' => null,
                'capabilities' => json_encode(['line_maintenance', 'a_check', 'b_check', 'unscheduled']),
                'hourly_rate_usd' => 85.00,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'African Aviation Services',
                'code' => 'AAS',
                'description' => 'Major MRO provider in East Africa',
                'type' => 'external',
                'contact_person' => 'John Mwangi',
                'email' => 'maintenance@africanaviation.com',
                'phone' => '+255-22-123-4567',
                'address' => null,
                'certification_number' => 'TCCA-AMO-001',
                'certification_expiry' => null,
                'capabilities' => json_encode(['c_check', 'd_check', 'major_repair', 'modification']),
                'hourly_rate_usd' => 120.00,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Quick Turn Maintenance',
                'code' => 'QTM',
                'description' => 'Line maintenance and AOG support',
                'type' => 'external',
                'contact_person' => 'Sarah Johnson',
                'email' => 'ops@quickturn.co.tz',
                'phone' => '+255-22-987-6543',
                'address' => null,
                'certification_number' => null,
                'certification_expiry' => null,
                'capabilities' => json_encode(['line_maintenance', 'aog_support', 'component_change']),
                'hourly_rate_usd' => 95.00,
                'is_active' => true,
                'sort_order' => 3,
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
        Schema::dropIfExists('maintenance_organizations');
    }
};
