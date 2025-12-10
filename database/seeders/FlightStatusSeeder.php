<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FlightStatus;

class FlightStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Scheduled',
                'code' => 'SCHEDULED',
                'description' => 'Flight is scheduled and waiting for departure',
                'color' => '#007bff',
                'sort_order' => 1,
            ],
            [
                'name' => 'Boarding',
                'code' => 'BOARDING',
                'description' => 'Passengers are boarding the aircraft',
                'color' => '#ffc107',
                'sort_order' => 2,
            ],
            [
                'name' => 'Departed',
                'code' => 'DEPARTED',
                'description' => 'Flight has departed from origin airport',
                'color' => '#17a2b8',
                'sort_order' => 3,
            ],
            [
                'name' => 'In Flight',
                'code' => 'IN_FLIGHT',
                'description' => 'Flight is currently in the air',
                'color' => '#28a745',
                'sort_order' => 4,
            ],
            [
                'name' => 'Arrived',
                'code' => 'ARRIVED',
                'description' => 'Flight has arrived at destination airport',
                'color' => '#20c997',
                'sort_order' => 5,
            ],
            [
                'name' => 'Delayed',
                'code' => 'DELAYED',
                'description' => 'Flight departure is delayed',
                'color' => '#fd7e14',
                'sort_order' => 6,
            ],
            [
                'name' => 'Cancelled',
                'code' => 'CANCELLED',
                'description' => 'Flight has been cancelled',
                'color' => '#dc3545',
                'sort_order' => 7,
            ],
            [
                'name' => 'Diverted',
                'code' => 'DIVERTED',
                'description' => 'Flight has been diverted to another airport',
                'color' => '#6f42c1',
                'sort_order' => 8,
            ],
        ];

        foreach ($statuses as $status) {
            FlightStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }

        $this->command->info('Flight statuses seeded successfully!');
    }
}
