<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContainerPermissionSeeder extends Seeder
{

    private string $model = 'container';


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
                'description' => 'Crear contenedor'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar contenedor'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar contenedor'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle de un contenedor'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos los contenedores'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();


        $user = User::find(1);
        $module = Module::where('slug', 'configuracion')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Container',
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
