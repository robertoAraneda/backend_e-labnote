<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SamplingIndicationPermissionSeeder extends Seeder
{
    private string $model = 'samplingIndication';


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
                'description' => 'Crear indicación toma muestra'
            ],
            [
                'name' => "{$this->model}.update",
                'action' => 'update',
                'description' => 'Modificar indicación toma muestra'
            ],
            [
                'name' => "{$this->model}.delete",
                'action' => 'delete',
                'description' => 'Eliminar indicación toma muestra'
            ],
            [
                'name' => "{$this->model}.show",
                'action' => 'show',
                'description' => 'Ver detalle indicación toma muestra'
            ],
            [
                'name' => "{$this->model}.index",
                'action' => 'index',
                'description' => 'Ver todos indicación toma muestra'
            ],
        ];

        foreach ($permissions as $key => $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'SampleType',
                'action' => $permission['action'],
                'description' => $permission['description']
            ]);
        }
    }
}
