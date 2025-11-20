<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = Admin::create([
            'first_name' => 'Super',
            'middle_name' => null,
            'last_name' => 'Admin',
            'email' => 'admin@gogis.gov.gm.ng',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('Super Admin');

        // Create DG Account
        $dg = Admin::create([
            'first_name' => 'Kabiru',
            'middle_name' => 'Usman',
            'last_name' => 'Hassan',
            'email' => 'dg@gogis.gov.gm.ng',
            'password' => Hash::make('password'),
        ]);
        $dg->assignRole('Super Admin');

        // Create Content Manager
        $contentManager = Admin::create([
            'first_name' => 'Content',
            'middle_name' => null,
            'last_name' => 'Manager',
            'email' => 'content@gogis.gov.gm.ng',
            'password' => Hash::make('password'),
        ]);
        $contentManager->assignRole('Content Manager');

        // Create Finance Officer
        $financeOfficer = Admin::create([
            'first_name' => 'Finance',
            'middle_name' => null,
            'last_name' => 'Officer',
            'email' => 'finance@gogis.gov.gm.ng',
            'password' => Hash::make('password'),
        ]);
        $financeOfficer->assignRole('Finance Officer');
    }
}
