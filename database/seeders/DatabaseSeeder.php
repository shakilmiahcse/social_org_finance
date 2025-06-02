<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissions = [
            'funds.create',
            'funds.view',
            'funds.edit',
            'funds.delete',
            'donors.create',
            'donors.view',
            'donors.edit',
            'donors.delete',
            'transactions.create',
            'transactions.view',
            'transactions.edit',
            'transactions.delete',
            'adjustments.create',
            'adjustments.view',
            'adjustments.edit',
            'adjustments.delete',
        ];

        // Permission গুলো global, তাই শুধু name এবং guard_name দিন
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Role তৈরি - organization_id = 1
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'organization_id' => 1,
            'guard_name' => 'web',
        ]);

        $managerRole = Role::firstOrCreate([
            'name' => 'manager',
            'organization_id' => 1,
            'guard_name' => 'web',
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'organization_id' => 1,
            'guard_name' => 'web',
        ]);

        // Admin gets all permissions
        $adminRole->syncPermissions(Permission::all());

        // Manager gets view-only permissions
        $managerRole->syncPermissions([
            'funds.view',
            'donors.view',
            'transactions.view',
            'adjustments.view',
        ]);
    }
}
