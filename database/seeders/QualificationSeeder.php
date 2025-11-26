<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qualifications = [
            [
                'name' => 'Certificate',
                'level' => 'Certificate',
                'rank' => 1,
                'description' => 'Professional or vocational certificate program',
                'is_active' => true,
            ],
            [
                'name' => 'Diploma',
                'level' => 'Diploma',
                'rank' => 2,
                'description' => 'Two or three-year diploma program',
                'is_active' => true,
            ],
            [
                'name' => 'Advanced Diploma',
                'level' => 'Advanced Diploma',
                'rank' => 3,
                'description' => 'Higher level diploma with specialized focus',
                'is_active' => true,
            ],
            [
                'name' => "Bachelor's Degree",
                'level' => 'Degree',
                'rank' => 4,
                'description' => 'Undergraduate bachelor degree (3-4 years)',
                'is_active' => true,
            ],
            [
                'name' => "Master's Degree",
                'level' => 'Masters',
                'rank' => 5,
                'description' => 'Postgraduate master degree (1-2 years)',
                'is_active' => true,
            ],
            [
                'name' => 'PhD/Doctorate',
                'level' => 'PhD',
                'rank' => 6,
                'description' => 'Doctoral degree - highest academic qualification',
                'is_active' => true,
            ],
        ];

        DB::table('qualifications')->insert(array_map(function ($qualification) {
            return array_merge($qualification, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $qualifications));
    }
}