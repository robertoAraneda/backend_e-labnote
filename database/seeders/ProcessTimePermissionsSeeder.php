<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ProcessTimePermissionsSeeder extends Seeder
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
                'name' => 'processTime.create',
                'action' => 'create',
                'description' => 'Crear tiempo de proceso'
            ],
            [
                'name' => 'processTime.update',
                'action' => 'update',
                'description' => 'Modificar tiempo de proceso'
            ],
            [
                'name' => 'processTime.delete',
                'action' => 'delete',
                'description' => 'Eliminar tiempo de proceso'
            ],
            [
                'name' => 'processTime.show',
                'action' => 'show',
                'description' => 'Ver detalle de un tiempo de proceso'
            ],
            [
                'name' => 'processTime.index',
                'action' => 'index',
                'description' => 'Ver todos los tiempos de proceso'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'ProcessTime',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

            $role->givePermissionTo($permission['name']);
        }
    }
}
