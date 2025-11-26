<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employmentTypes = [
            [
                'name' => 'Full-time',
                'code' => 'FT',
                'description' => 'Permanent full-time employment with standard benefits and working hours',
                'requires_contract_dates' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Part-time',
                'code' => 'PT',
                'description' => 'Permanent part-time employment with reduced working hours',
                'requires_contract_dates' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Contract',
                'code' => 'CNT',
                'description' => 'Fixed-term contract employment with specific start and end dates',
                'requires_contract_dates' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Intern',
                'code' => 'INT',
                'description' => 'Temporary internship position for learning and development',
                'requires_contract_dates' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Temporary',
                'code' => 'TMP',
                'description' => 'Short-term temporary employment for specific projects or coverage',
                'requires_contract_dates' => true,
                'is_active' => true,
            ],
        ];

        DB::table('employment_types')->insert(array_map(function ($employmentType) {
            return array_merge($employmentType, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $employmentTypes));
    }
}