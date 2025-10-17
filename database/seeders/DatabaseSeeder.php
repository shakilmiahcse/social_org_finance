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
            'users.create',
            'users.view',
            'users.edit',
            'users.delete',
            'funds.create',
            'funds.view',
            'funds.edit',
            'funds.delete',
            'funds.close',
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
            'settings.view',
            'settings.update',
            'reports.view',
            'reports.export',
            'reports.generate',
            'dashboard.view',
            'roles.create',
            'roles.view',
            'roles.edit',
            'roles.delete'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

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
