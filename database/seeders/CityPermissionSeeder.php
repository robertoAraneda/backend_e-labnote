<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class CityPermissionSeeder extends Seeder
{
    private string $model = 'city';


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
                'description' => 'Crear comuna'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar comuna'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar comuna'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle  comuna'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos comuna'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        foreach ($permissions as $key => $permission) {
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'City',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);

            $role->givePermissionTo($permission['name']);
        }
    }
}
