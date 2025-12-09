<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fuel units should already exist from migration, but check if empty
        if (!\App\Models\FuelUnit::exists()) {
            \App\Models\FuelUnit::create(['name' => 'Liters', 'code' => 'L', 'symbol' => 'L', 'is_active' => true]);
            \App\Models\FuelUnit::create(['name' => 'US Gallons', 'code' => 'GAL_US', 'symbol' => 'USG', 'is_active' => true]);
        }

        // Create sample currencies if they don't exist
        if (!\App\Models\Currency::exists()) {
            \App\Models\Currency::create(['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$']);
            \App\Models\Currency::create(['name' => 'Tanzanian Shilling', 'code' => 'TZS', 'symbol' => 'TSh']);
        }

        // Create sample payment statuses if they don't exist
        if (!\App\Models\PaymentStatus::exists()) {
            \App\Models\PaymentStatus::create(['name' => 'Pending', 'code' => 'PENDING']);
            \App\Models\PaymentStatus::create(['name' => 'Paid', 'code' => 'PAID']);
            \App\Models\PaymentStatus::create(['name' => 'Overdue', 'code' => 'OVERDUE']);
        }

        // Create sample aircraft manufacturers and models if they don't exist
        if (!\App\Models\AircraftManufacturer::exists()) {
            $manufacturer = \App\Models\AircraftManufacturer::create(['name' => 'Boeing']);
            \App\Models\AircraftModel::create([
                'name' => '737-800',
                'manufacturer_id' => $manufacturer->id,
                'aircraft_type' => 'Commercial',
                'mtow' => 79000
            ]);
        }

        // Create sample aircraft if it doesn't exist
        if (!\App\Models\Aircraft::exists()) {
            $manufacturer = \App\Models\AircraftManufacturer::first();
            $model = \App\Models\AircraftModel::first();

            \App\Models\Aircraft::create([
                'registration_number' => '5H-ZFT',
                'manufacturer_id' => $manufacturer->id,
                'model_id' => $model->id,
                'year_manufactured' => 2020,
                'seating_capacity' => 160,
                'is_active' => true
            ]);
        }

        // Create sample airports if they don't exist
        if (!\App\Models\Airport::exists()) {
            \App\Models\Airport::create([
                'name' => 'Julius Nyerere International Airport',
                'icao_code' => 'HTDA',
                'iata_code' => 'DAR',
                'city' => 'Dar es Salaam',
                'country' => 'Tanzania'
            ]);

            \App\Models\Airport::create([
                'name' => 'Kilimanjaro International Airport',
                'icao_code' => 'HTKJ',
                'iata_code' => 'JRO',
                'city' => 'Kilimanjaro',
                'country' => 'Tanzania'
            ]);
        }
    }
}
