<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workLocations = [
            [
                'name' => 'Zanzibar Airport',
                'code' => 'ZNZ',
                'address' => 'Abeid Amani Karume International Airport',
                'city' => 'Zanzibar',
                'type' => 'Airport',
                'is_active' => true,
            ],
            [
                'name' => 'Dar es Salaam Airport',
                'code' => 'DAR',
                'address' => 'Julius Nyerere International Airport',
                'city' => 'Dar es Salaam',
                'type' => 'Airport',
                'is_active' => true,
            ],
            [
                'name' => 'Head Office Zanzibar',
                'code' => 'HOZ',
                'address' => 'Stone Town Business District',
                'city' => 'Zanzibar',
                'type' => 'Office',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance Facility',
                'code' => 'MNT',
                'address' => 'Aircraft Maintenance Hangar',
                'city' => 'Zanzibar',
                'type' => 'Facility',
                'is_active' => true,
            ],
            [
                'name' => 'Training Center',
                'code' => 'TRC',
                'address' => 'Aviation Training Complex',
                'city' => 'Zanzibar',
                'type' => 'Training',
                'is_active' => true,
            ],
        ];

        DB::table('work_locations')->insert(array_map(function ($location) {
            return array_merge($location, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $workLocations));
    }
}