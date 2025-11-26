<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalClass;

class MedicalClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicalClasses = [
            [
                'name' => 'Class 1 Medical',
                'code' => 'CLASS_1',
                'description' => 'First Class Medical Certificate - highest medical standard for airline transport pilots and commercial pilots over 40',
                'validity_period_months' => 6, // 6 months for pilots over 40, 12 months under 40
                'privileges' => [
                    'ATPL privileges - airline transport operations',
                    'CPL privileges - commercial operations',
                    'PPL privileges - private operations',
                    'Flight instructor privileges'
                ],
                'limitations' => [
                    'Age-based validity periods (6 months over 40, 12 months under 40)',
                    'Regular medical examinations required',
                    'Immediate grounding if medical condition develops'
                ],
                'requires_renewal' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Class 2 Medical',
                'code' => 'CLASS_2',
                'description' => 'Second Class Medical Certificate - for commercial pilots under 40 and flight instructors',
                'validity_period_months' => 12,
                'privileges' => [
                    'CPL privileges - commercial operations',
                    'PPL privileges - private operations',
                    'Flight instructor privileges',
                    'Multi-crew operations as co-pilot'
                ],
                'limitations' => [
                    'No ATPL privileges - cannot act as captain in airline operations',
                    'Age restrictions for commercial operations',
                    'Cannot exercise ATPL privileges after age 40'
                ],
                'requires_renewal' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Class 3 Medical',
                'code' => 'CLASS_3',
                'description' => 'Third Class Medical Certificate - for private pilots and student pilots',
                'validity_period_months' => 24, // 24 months under 40, 12 months over 40
                'privileges' => [
                    'PPL privileges - private operations only',
                    'Student pilot training privileges',
                    'Recreational flying privileges'
                ],
                'limitations' => [
                    'No commercial privileges - cannot receive compensation',
                    'Limited to private operations only',
                    'Cannot instruct or conduct commercial operations',
                    'Age-based validity (24 months under 40, 12 months over 40)'
                ],
                'requires_renewal' => true,
                'is_active' => true,
            ],
        ];

        foreach ($medicalClasses as $medicalClass) {
            MedicalClass::updateOrCreate(
                ['code' => $medicalClass['code']],
                $medicalClass
            );
        }
    }
}
