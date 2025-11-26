<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LicenseType;

class LicenseTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $licenseTypes = [
            [
                'name' => 'Commercial Pilot License',
                'code' => 'CPL',
                'description' => 'Commercial Pilot License allowing commercial operations with aircraft',
                'renewal_period_months' => 24,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date',
                    'document_file'
                ],
                'requires_medical' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Airline Transport Pilot License',
                'code' => 'ATPL',
                'description' => 'Airline Transport Pilot License for airline and charter operations',
                'renewal_period_months' => 24,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date',
                    'document_file'
                ],
                'requires_medical' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Private Pilot License',
                'code' => 'PPL',
                'description' => 'Private Pilot License for recreational and private flying',
                'renewal_period_months' => 24,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date',
                    'document_file'
                ],
                'requires_medical' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Instrument Rating',
                'code' => 'IR',
                'description' => 'Instrument Rating qualification for IFR operations',
                'renewal_period_months' => 12,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date',
                    'document_file'
                ],
                'requires_medical' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Multi-Engine Piston',
                'code' => 'MEP',
                'description' => 'Multi-Engine Piston aircraft class rating',
                'renewal_period_months' => 24,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date'
                ],
                'requires_medical' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Single Engine Piston',
                'code' => 'SEP',
                'description' => 'Single Engine Piston aircraft class rating',
                'renewal_period_months' => 24,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date'
                ],
                'requires_medical' => false,
                'is_active' => true,
            ],
            [
                'name' => 'RNAV Approach',
                'code' => 'RNAV',
                'description' => 'Area Navigation (RNAV) approach qualification',
                'renewal_period_months' => 12,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date'
                ],
                'requires_medical' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Flight Instructor Rating',
                'code' => 'FI',
                'description' => 'Flight Instructor Rating for teaching and training',
                'renewal_period_months' => 36,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date',
                    'document_file'
                ],
                'requires_medical' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Type Rating',
                'code' => 'TR',
                'description' => 'Aircraft Type Rating for specific aircraft types',
                'renewal_period_months' => 12,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date',
                    'expiry_date',
                    'aircraft_type'
                ],
                'requires_medical' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Other License/Rating',
                'code' => 'OTHER',
                'description' => 'Other aviation licenses, ratings or certifications',
                'renewal_period_months' => null,
                'required_fields' => [
                    'license_number',
                    'issuing_authority',
                    'issue_date'
                ],
                'requires_medical' => false,
                'is_active' => true,
            ],
        ];

        foreach ($licenseTypes as $licenseType) {
            LicenseType::updateOrCreate(
                ['code' => $licenseType['code']],
                $licenseType
            );
        }
    }
}
