<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProficiencyType;

class ProficiencyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proficiencyTypes = [
            [
                'name' => 'English Proficiency Level',
                'code' => 'EPL',
                'description' => 'ICAO English Proficiency Level (Levels 1-6). Required for international aviation operations.',
                'renewal_period_months' => 36,
                'has_levels' => true,
                'required_fields' => [
                    'epl_level',
                    'testing_authority',
                    'certificate_number',
                    'issue_date',
                    'expiry_date'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Instrument Rating',
                'code' => 'IR',
                'description' => 'Instrument Rating proficiency check for IFR operations.',
                'renewal_period_months' => 12,
                'has_levels' => false,
                'required_fields' => [
                    'rating_type',
                    'renewal_status',
                    'issue_date',
                    'expiry_date',
                    'examiner_name'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Operator Proficiency Check',
                'code' => 'OPC',
                'description' => 'Operator Proficiency Check required for commercial operations.',
                'renewal_period_months' => 6,
                'has_levels' => false,
                'required_fields' => [
                    'check_type',
                    'check_status',
                    'examiner_name',
                    'last_completion_date',
                    'next_due_date'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Line Check',
                'code' => 'LINE_CHECK',
                'description' => 'Line check conducted during regular flight operations.',
                'renewal_period_months' => 12,
                'has_levels' => false,
                'required_fields' => [
                    'route_sector',
                    'check_status',
                    'examiner_name',
                    'last_completion_date',
                    'next_due_date'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Dangerous Goods Training',
                'code' => 'DG',
                'description' => 'IATA Dangerous Goods Regulations training for cargo operations.',
                'renewal_period_months' => 24,
                'has_levels' => false,
                'required_fields' => [
                    'training_provider',
                    'certificate_number',
                    'last_completion_date',
                    'next_due_date'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Safety Management System',
                'code' => 'SMS',
                'description' => 'Safety Management System training and certification.',
                'renewal_period_months' => 36,
                'has_levels' => false,
                'required_fields' => [
                    'training_provider',
                    'certificate_number',
                    'last_completion_date',
                    'next_due_date'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Crew Resource Management',
                'code' => 'CRM',
                'description' => 'Crew Resource Management training for effective teamwork and communication.',
                'renewal_period_months' => 36,
                'has_levels' => false,
                'required_fields' => [
                    'training_provider',
                    'certificate_number',
                    'last_completion_date',
                    'next_due_date'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($proficiencyTypes as $proficiencyType) {
            ProficiencyType::updateOrCreate(
                ['code' => $proficiencyType['code']],
                $proficiencyType
            );
        }
    }
}
