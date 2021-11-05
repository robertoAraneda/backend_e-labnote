<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SamplingConditionPermissionsSeeder extends Seeder
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
                'name' => 'samplingCondition.create',
                'action' => 'create',
                'description' => 'Crear condición toma de muestra'
            ],
            [
                'name' => 'samplingCondition.update',
                'action' => 'update',
                'description' => 'Modificar condición toma de muestra'
            ],
            [
                'name' => 'samplingCondition.delete',
                'action' => 'delete',
                'description' => 'Eliminar condición toma de muestra'
            ],
            [
                'name' => 'samplingCondition.show',
                'action' => 'show',
                'description' => 'Ver detalle de una condición toma de muestra'
            ],
            [
                'name' => 'samplingCondition.index',
                'action' => 'index',
                'description' => 'Ver todas las condiciones toma de muestra'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();


        $user = User::find(1);
        $module = Module::where('slug', 'configuracion')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'SamplingCondition',
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
