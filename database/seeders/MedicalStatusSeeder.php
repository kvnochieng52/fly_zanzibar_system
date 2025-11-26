<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MedicalStatus;

class MedicalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Valid',
                'code' => 'valid',
                'color' => 'success',
                'description' => 'Medical certificate is valid and current',
                'is_active_status' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Expired',
                'code' => 'expired',
                'color' => 'danger',
                'description' => 'Medical certificate has expired',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Suspended',
                'code' => 'suspended',
                'color' => 'warning',
                'description' => 'Medical certificate is temporarily suspended',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Revoked',
                'code' => 'revoked',
                'color' => 'dark',
                'description' => 'Medical certificate has been permanently revoked',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Under Review',
                'code' => 'under_review',
                'color' => 'warning',
                'description' => 'Medical certificate is under medical authority review',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Restricted',
                'code' => 'restricted',
                'color' => 'warning',
                'description' => 'Medical certificate has operational restrictions or limitations',
                'is_active_status' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Due Soon',
                'code' => 'due_soon',
                'color' => 'warning',
                'description' => 'Medical certificate expires within 30 days',
                'is_active_status' => true,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            MedicalStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
