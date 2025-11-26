<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Staff module reference tables - run in order due to dependencies
        $this->call([
            GenderSeeder::class,
            DepartmentSeeder::class,
            StaffStatusSeeder::class,
            PositionSeeder::class, // Must run after DepartmentSeeder due to foreign key
        ]);

        // Aviation certification reference tables
        $this->call([
            ProficiencyTypeSeeder::class,
            LicenseTypeSeeder::class,
            LicenseStatusSeeder::class,
            MedicalClassSeeder::class,
            MedicalStatusSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
