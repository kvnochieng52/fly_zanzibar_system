<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AirportCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $airports = [
            [
                'iata_code' => 'DAR',
                'latitude' => -6.8781,
                'longitude' => 39.2026,
            ],
            [
                'iata_code' => 'ZNZ',
                'latitude' => -6.2220,
                'longitude' => 39.2249,
            ],
            [
                'iata_code' => 'JRO',
                'latitude' => -3.4299,
                'longitude' => 37.0744,
            ],
        ];

        foreach ($airports as $airport) {
            DB::table('airports')
                ->where('iata_code', $airport['iata_code'])
                ->update([
                    'latitude' => $airport['latitude'],
                    'longitude' => $airport['longitude'],
                ]);
        }

        $this->command->info('Airport coordinates updated successfully!');
    }
}
