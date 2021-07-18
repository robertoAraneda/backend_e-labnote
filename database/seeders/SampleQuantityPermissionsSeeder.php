<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SampleQuantityPermissionsSeeder extends Seeder
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
                'name' => 'sampleQuantity.create',
                'action' => 'create',
                'description' => 'Crear cantidad de muestra'
            ],
            [
                'name' => 'sampleQuantity.update',
                'action' => 'update',
                'description' => 'Modificar cantidad de muestra'
            ],
            [
                'name' => 'sampleQuantity.delete',
                'action' => 'delete',
                'description' => 'Eliminar cantidad de muestra'
            ],
            [
                'name' => 'sampleQuantity.show',
                'action' => 'show',
                'description' => 'Ver detalle de una cantidad de muestra'
            ],
            [
                'name' => 'sampleQuantity.index',
                'action' => 'index',
                'description' => 'Ver todos las cantidades de muestra'
            ],
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'SampleQuantity',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

        }
    }
}
