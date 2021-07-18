<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class ResponseTimePermissionsSeeder extends Seeder
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
                'name' => 'responseTime.create',
                'action' => 'create',
                'description' => 'Crear tiempo de respuesta'
            ],
            [
                'name' => 'responseTime.update',
                'action' => 'update',
                'description' => 'Modificar tiempo de respuesta'
            ],
            [
                'name' => 'responseTime.delete',
                'action' => 'delete',
                'description' => 'Eliminar tiempo de respuesta'
            ],
            [
                'name' => 'responseTime.show',
                'action' => 'show',
                'description' => 'Ver detalle de un tiempo de respuesta'
            ],
            [
                'name' => 'responseTime.index',
                'action' => 'index',
                'description' => 'Ver todos los tiempos de respuesta'
            ],
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'ResponseTime',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

        }
    }
}
