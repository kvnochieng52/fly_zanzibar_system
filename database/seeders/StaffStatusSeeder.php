<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Active',
                'code' => 'ACTIVE',
                'description' => 'Employee is currently active and working',
                'color' => '#28a745', // Green
                'is_active' => true,
                'allows_access' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'On Leave',
                'code' => 'LEAVE',
                'description' => 'Employee is on approved leave',
                'color' => '#ffc107', // Yellow
                'is_active' => true,
                'allows_access' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suspended',
                'code' => 'SUSPENDED',
                'description' => 'Employee is temporarily suspended',
                'color' => '#fd7e14', // Orange
                'is_active' => true,
                'allows_access' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Resigned',
                'code' => 'RESIGNED',
                'description' => 'Employee has resigned from the company',
                'color' => '#6c757d', // Gray
                'is_active' => true,
                'allows_access' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Terminated',
                'code' => 'TERMINATED',
                'description' => 'Employee has been terminated',
                'color' => '#dc3545', // Red
                'is_active' => true,
                'allows_access' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Probation',
                'code' => 'PROBATION',
                'description' => 'Employee is on probationary period',
                'color' => '#17a2b8', // Info blue
                'is_active' => true,
                'allows_access' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Medical Leave',
                'code' => 'MEDICAL',
                'description' => 'Employee is on medical leave',
                'color' => '#e83e8c', // Pink
                'is_active' => true,
                'allows_access' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('staff_statuses')->insert($statuses);
    }
}
