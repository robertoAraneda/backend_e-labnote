<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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
                'description' => 'Ver detalle de una disponibilidad'
            ],
            [
                'name' => 'availability.index',
                'action' => 'index',
                'description' => 'Ver todas las disponibilidades'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        $user = User::find(1);
        $module = Module::where('slug', 'configuracion')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Availability',
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
