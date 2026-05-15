<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        $adminUser = User::where('email', 'superadmin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('Super Admin');
        }
    }
}
