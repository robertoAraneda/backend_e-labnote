<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SlotPermissionsSeeder extends Seeder
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
                'name' => 'slot.create',
                'action' => 'create',
                'description' => 'Crear slot'
            ],
            [
                'name' => 'slot.update',
                'action' => 'update',
                'description' => 'Modificar slot'
            ],
            [
                'name' => 'slot.delete',
                'action' => 'delete',
                'description' => 'Eliminar slot'
            ],
            [
                'name' => 'slot.show',
                'action' => 'show',
                'description' => 'Ver detalle de slot'
            ],
            [
                'name' => 'slot.index',
                'action' => 'index',
                'description' => 'Ver todos los slot'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();
        $user = User::find(1);
        $module = Module::where('slug', 'configuracion')->first();

        foreach ($permissions as $permission){
            $permission =  Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Slot',
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
