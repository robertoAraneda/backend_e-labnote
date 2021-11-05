<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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

        $user = User::find(1);
        $module = Module::where('slug', 'configuracion')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'MedicalRequestType',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

            ModulePermission::create([
                'module_id' => $module->id,
                'permission_id' => $permission->id,
                'user_id' => $user->id,
            ]);

            $role->givePermissionTo($permission['name']);
        }

    }
}
