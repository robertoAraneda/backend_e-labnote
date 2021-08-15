<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class MedicalRequestTypePermissionsSeeder extends Seeder
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
                'name' => 'medicalRequestType.create',
                'action' => 'create',
                'description' => 'Crear tipo de solicitud médica'
            ],
            [
                'name' => 'medicalRequestType.update',
                'action' => 'update',
                'description' => 'Modificar tipo de solicitud médica'
            ],
            [
                'name' => 'medicalRequestType.delete',
                'action' => 'delete',
                'description' => 'Eliminar tipo de solicitud médica'
            ],
            [
                'name' => 'medicalRequestType.show',
                'action' => 'show',
                'description' => 'Ver detalle de un tipo de solicitud médica'
            ],
            [
                'name' => 'medicalRequestType.index',
                'action' => 'index',
                'description' => 'Ver todos los tipo de solicitud médica'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'MedicalRequestType',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

            $role->givePermissionTo($permission['name']);

        }
    }
}
