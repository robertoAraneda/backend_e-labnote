<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AvailabilityPermissionsSeeder extends Seeder
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
                'name' => 'availability.create',
                'action' => 'create',
                'description' => 'Crear disponibilidad'
            ],
            [
                'name' => 'availability.update',
                'action' => 'update',
                'description' => 'Modificar disponibilidad'
            ],
            [
                'name' => 'availability.delete',
                'action' => 'delete',
                'description' => 'Eliminar disponibilidad'
            ],
            [
                'name' => 'availability.show',
                'action' => 'show',
                'description' => 'Ver detalle disponibilidad'
            ],
            [
                'name' => 'availability.index',
                'action' => 'index',
                'description' => 'Ver todas las disponibilidades'
            ],
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Availability',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

        }
    }
}
