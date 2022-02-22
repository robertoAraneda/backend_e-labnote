<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class NobilisAnalytePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'nobilisAnalyte.create',
                'action' => 'create',
                'description' => 'Crear exámenes LIS Nobilis'
            ],
            [
                'name' => 'nobilisAnalyte.update',
                'action' => 'update',
                'description' => 'Modificar exámenes LIS Nobilis'
            ],
            [
                'name' => 'nobilisAnalyte.delete',
                'action' => 'delete',
                'description' => 'Eliminar exámenes LIS Nobilis'
            ],
            [
                'name' => 'nobilisAnalyte.show',
                'action' => 'show',
                'description' => 'Ver detalle de un examen LIS Nobilis'
            ],
            [
                'name' => 'nobilisAnalyte.index',
                'action' => 'index',
                'description' => 'Ver todos los exámenes LIS Nobiliso'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();
        $user = User::find(1);
        $module = Module::where('slug', 'configuracion-avanzada')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'NobilisAnalyte',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

            ModulePermission::create([
                'module_id' => $module->id,
                'permission_id' => $permission->id,
                'user_id' => $user->id,
            ]);

            $role->givePermissionTo($permission['name']);
        }
    }
}
