<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'permission.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'permission.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'permission.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'permission.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'permission.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'role.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'role.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'role.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'role.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'role.show', 'guard_name' => 'api']);
        Permission::create(['name' => 'user.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'user.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'user.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'user.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'user.show', 'guard_name' => 'api']);
    }
}
