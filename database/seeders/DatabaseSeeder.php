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

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // রোল তৈরি
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name' => 'manager']);
        $userRole = Role::create(['name' => 'user']);

        // অ্যাডমিনকে সব পারমিশন দিন
        $adminRole->givePermissionTo(Permission::all());

        // ম্যানেজারকে কিছু পারমিশন দিন
        $managerRole->givePermissionTo([
            'funds.view',
            'donors.view',
            'transactions.view',
            'adjustments.view',
        ]);
    }
}
