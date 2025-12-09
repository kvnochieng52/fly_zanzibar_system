<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // Permission Management
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // Staff Management
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',

            // Aircraft Management
            'aircraft.view',
            'aircraft.create',
            'aircraft.edit',
            'aircraft.delete',

            // Financial Management
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'receipts.view',
            'receipts.create',
            'receipts.edit',
            'receipts.delete',
            'statements.view',

            // Fee Tracking
            'landing-fees.view',
            'landing-fees.create',
            'landing-fees.edit',
            'landing-fees.delete',
            'navigation-fees.view',
            'navigation-fees.create',
            'navigation-fees.edit',
            'navigation-fees.delete',
            'fuel-purchases.view',
            'fuel-purchases.create',
            'fuel-purchases.edit',
            'fuel-purchases.delete',
            'fuel-consumption.view',
            'fuel-consumption.create',
            'fuel-consumption.edit',
            'fuel-consumption.delete',

            // System Administration
            'admin.dashboard',
            'admin.settings',
            'admin.reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $operatorRole = Role::firstOrCreate(['name' => 'Operator']);
        $viewerRole = Role::firstOrCreate(['name' => 'Viewer']);

        // Assign Permissions to Roles

        // Super Admin - All permissions
        $superAdminRole->syncPermissions(Permission::all());

        // Admin - Most permissions except super admin actions
        $adminPermissions = Permission::whereNotIn('name', [
            'users.delete', // Cannot delete users
            'roles.delete', // Cannot delete roles
            'permissions.delete', // Cannot delete permissions
        ])->pluck('name');
        $adminRole->syncPermissions($adminPermissions);

        // Manager - Business operations
        $managerPermissions = [
            'staff.view', 'staff.create', 'staff.edit',
            'aircraft.view', 'aircraft.create', 'aircraft.edit',
            'invoices.view', 'invoices.create', 'invoices.edit',
            'receipts.view', 'receipts.create', 'receipts.edit',
            'statements.view',
            'landing-fees.view', 'landing-fees.create', 'landing-fees.edit',
            'navigation-fees.view', 'navigation-fees.create', 'navigation-fees.edit',
            'fuel-purchases.view', 'fuel-purchases.create', 'fuel-purchases.edit',
            'fuel-consumption.view', 'fuel-consumption.create', 'fuel-consumption.edit',
            'admin.dashboard', 'admin.reports',
        ];
        $managerRole->syncPermissions($managerPermissions);

        // Operator - Day-to-day operations
        $operatorPermissions = [
            'staff.view',
            'aircraft.view',
            'invoices.view', 'invoices.create',
            'receipts.view', 'receipts.create',
            'statements.view',
            'landing-fees.view', 'landing-fees.create',
            'navigation-fees.view', 'navigation-fees.create',
            'fuel-purchases.view', 'fuel-purchases.create',
            'fuel-consumption.view', 'fuel-consumption.create',
            'admin.dashboard',
        ];
        $operatorRole->syncPermissions($operatorPermissions);

        // Viewer - Read-only access
        $viewerPermissions = [
            'staff.view',
            'aircraft.view',
            'invoices.view',
            'receipts.view',
            'statements.view',
            'landing-fees.view',
            'navigation-fees.view',
            'fuel-purchases.view',
            'fuel-consumption.view',
            'admin.dashboard',
        ];
        $viewerRole->syncPermissions($viewerPermissions);

        // Assign the Super Admin role to the existing user
        $existingUser = User::first();
        if ($existingUser) {
            $existingUser->assignRole('Super Admin');
            echo "Assigned 'Super Admin' role to user: {$existingUser->name}\n";
        }

        echo "User Management seeding completed successfully!\n";
        echo "Created " . Permission::count() . " permissions\n";
        echo "Created " . Role::count() . " roles\n";
    }
}