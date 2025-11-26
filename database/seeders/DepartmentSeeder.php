<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Flight Operations',
                'code' => 'OPS',
                'description' => 'Manages flight operations, crew scheduling, and aircraft operations',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aircraft Maintenance',
                'code' => 'MAINT',
                'description' => 'Responsible for aircraft maintenance, inspections, and repairs',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Finance & Accounting',
                'code' => 'FIN',
                'description' => 'Handles financial operations, accounting, invoicing, and receipts',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ground Services',
                'code' => 'GND',
                'description' => 'Ground handling, baggage, cargo, and passenger services',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Management',
                'code' => 'MGMT',
                'description' => 'Senior management and executive leadership',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Safety & Security',
                'code' => 'SAFE',
                'description' => 'Aviation safety, security protocols, and compliance',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Administration',
                'code' => 'ADMIN',
                'description' => 'Human resources, administration, and support functions',
                'manager_id' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('departments')->insert($departments);
    }
}
