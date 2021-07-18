<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionPermissionSeeder extends Seeder
{

    private string $model = 'permission';
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
                'description' => 'Crear permiso'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar permiso'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar permiso'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle de un permiso'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos los permiso'
            ],
        ];

        foreach ($permissions as $key => $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Permission',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);
        }
    }
}
