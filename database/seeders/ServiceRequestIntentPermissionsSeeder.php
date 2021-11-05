<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceRequestIntentPermissionsSeeder extends Seeder
{
    private string $model = 'serviceRequestIntent';


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => "{$this->model}.create",
                'action' => 'create',
                'description' => 'Crear intención ServiceRequest'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar intención ServiceRequest'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar intención ServiceRequest'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle intención ServiceRequest'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Lista intenciones ServiceRequest'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        $user = User::find(1);
        $module = Module::where('slug', 'configuracion-avanzada')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'ServiceRequestIntent',
                'action' => $permission['action'],
                'description' => $permission['description']
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
