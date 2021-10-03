<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SpecimenStatusPermissionsSeeder extends Seeder
{
    private string $model = 'specimenStatus';


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
                'description' => 'Crear estado tipo muestra'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar estado tipo muestra'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar estado tipo muestra'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle  estado de un tipo muestra'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos los estado tipos de muestras'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        foreach ($permissions as $key => $permission) {
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'SpecimenStatus',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);

            $role->givePermissionTo($permission['name']);
        }
    }
}
