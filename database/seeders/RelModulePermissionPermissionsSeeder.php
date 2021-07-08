<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class RelModulePermissionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'modulePermission.create',
            'modulePermission.index',
        ];

        foreach ($permissions as $permission){

            $temp = explode('.', $permission);
            Permission::create([
                'name' => $permission,
                'model' => 'Modulos-Permisos',
                'action' =>  $temp[1],
                'description' => "Se puede {$temp[1]} Modulos-Permisos",
                'guard_name' => 'api'
            ]);
        }
    }
}
