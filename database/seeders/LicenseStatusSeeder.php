<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LicenseStatus;

class LicenseStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Active',
                'code' => 'active',
                'color' => 'success',
                'description' => 'License is active and valid for operations',
                'is_active_status' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Expired',
                'code' => 'expired',
                'color' => 'danger',
                'description' => 'License has expired and requires renewal',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Suspended',
                'code' => 'suspended',
                'color' => 'warning',
                'description' => 'License is temporarily suspended by authority',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Revoked',
                'code' => 'revoked',
                'color' => 'dark',
                'description' => 'License has been permanently revoked',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Pending Renewal',
                'code' => 'pending_renewal',
                'color' => 'warning',
                'description' => 'License renewal application is in progress',
                'is_active_status' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Due Soon',
                'code' => 'due_soon',
                'color' => 'warning',
                'description' => 'License expires within 30 days',
                'is_active_status' => true,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            LicenseStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
