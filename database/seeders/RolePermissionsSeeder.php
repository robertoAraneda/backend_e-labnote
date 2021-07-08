<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = [
            'role.create',
            'role.update',
            'role.delete',
            'role.index',
            'role.show'
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }
    }
}
