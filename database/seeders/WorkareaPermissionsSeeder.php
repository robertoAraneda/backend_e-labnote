<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class WorkareaPermissionsSeeder extends Seeder
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
              'name' => 'workarea.create',
              'action' => 'create',
              'description' => 'Crear área de trabajo'
          ],
            [
                'name' => 'workarea.update',
                'action' => 'update',
                'description' => 'Modificar área de trabajo'
            ],
            [
                'name' => 'workarea.delete',
                'action' => 'delete',
                'description' => 'Eliminar área de trabajo'
            ],
            [
                'name' => 'workarea.show',
                'action' => 'show',
                'description' => 'Ver detalle de un área de trabajo'
            ],
            [
                'name' => 'workarea.index',
                'action' => 'index',
                'description' => 'Ver todas las áreas de trabajo'
            ],
        ];

        foreach ($permissions as $key => $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Workarea',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);
        }
    }
}
