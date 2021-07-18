<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class LaboratoryPermissionSeeder extends Seeder
{

    private string $model = 'laboratory';
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
                'description' => 'Crear laboratorio'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar laboratorio'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar laboratorio'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle de un laboratorio'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos los laboratorio'
            ],
        ];

        foreach ($permissions as $key => $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Laboratory',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);
        }
    }
}
