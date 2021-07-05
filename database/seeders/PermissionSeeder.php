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

        Permission::create(['name' => 'laboratory.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratory.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratory.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratory.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratory.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'rolePermission.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'rolePermission.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'rolePermission.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'rolePermission.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'rolePermission.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'module.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'module.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'module.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'module.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'module.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'menu.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'menu.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'menu.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'menu.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'menu.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'laboratoryModule.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratoryModule.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratoryModule.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratoryModule.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'laboratoryModule.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'workarea.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'workarea.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'workarea.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'workarea.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'workarea.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'disponibility.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'disponibility.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'disponibility.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'disponibility.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'disponibility.show', 'guard_name' => 'api']);

        Permission::create(['name' => 'processTime.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'processTime.update', 'guard_name' => 'api']);
        Permission::create(['name' => 'processTime.delete', 'guard_name' => 'api']);
        Permission::create(['name' => 'processTime.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'processTime.show', 'guard_name' => 'api']);
    }
}
