<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceRequestPriorityPermissionsSeeder extends Seeder
{
    private string $model = 'serviceRequestPriority';


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
                'description' => 'Crear prioridad ServiceRequest'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar prioridad ServiceRequest'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar prioridad ServiceRequest'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle  prioridad ServiceRequest'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Listar prioridad ServiceRequest'
            ],
        ];


        $role = Role::where('name', 'Administrador')->first();

        $user = User::find(1);
        $module = Module::where('slug', 'configuracion-avanzada')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'ServiceRequestPriority',
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
