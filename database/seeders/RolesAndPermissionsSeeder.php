<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // CMS Permissions
            'manage settings',
            'manage pages',
            'manage news',
            'manage events',
            'manage media gallery',

            // User Management
            'manage admins',
            'manage customers',
            'view customers',

            // Property Management
            'manage properties',
            'view properties',

            // Revenue Management
            'manage revenue heads',
            'manage revenue sub heads',
            'view revenue reports',

            // Invoice Management
            'create invoices',
            'edit invoices',
            'delete invoices',
            'view invoices',
            'download invoices',

            // Payment Management
            'view payments',
            'process payments',
            'refund payments',
            'download payment receipts',

            // System
            'view activity logs',
            'manage configurations',
            'view contact messages',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::create(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - has most permissions except critical system settings
        $admin = Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'manage pages',
            'manage news',
            'manage events',
            'manage media gallery',
            'manage customers',
            'view customers',
            'manage properties',
            'view properties',
            'manage revenue heads',
            'manage revenue sub heads',
            'view revenue reports',
            'create invoices',
            'edit invoices',
            'view invoices',
            'download invoices',
            'view payments',
            'process payments',
            'download payment receipts',
            'view contact messages',
        ]);

        // Content Manager - manages website content only
        $contentManager = Role::create(['name' => 'Content Manager', 'guard_name' => 'web']);
        $contentManager->givePermissionTo([
            'manage pages',
            'manage news',
            'manage events',
            'manage media gallery',
            'view contact messages',
        ]);

        // Finance Officer - manages invoices and payments
        $financeOfficer = Role::create(['name' => 'Finance Officer', 'guard_name' => 'web']);
        $financeOfficer->givePermissionTo([
            'view customers',
            'view properties',
            'view revenue reports',
            'create invoices',
            'edit invoices',
            'view invoices',
            'download invoices',
            'view payments',
            'process payments',
            'download payment receipts',
        ]);

        // Viewer - read-only access
        $viewer = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        $viewer->givePermissionTo([
            'view customers',
            'view properties',
            'view invoices',
            'view payments',
            'view revenue reports',
        ]);
    }
}
