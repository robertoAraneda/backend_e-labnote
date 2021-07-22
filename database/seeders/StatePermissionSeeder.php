<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class StatePermissionSeeder extends Seeder
{
    private string $model = 'state';


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
                'description' => 'Crear región'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar región'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar región'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle  región'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos región'
            ],
        ];

        foreach ($permissions as $key => $permission) {
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'State',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);
        }
    }
}
