<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
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

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Slot',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

            $role->givePermissionTo($permission['name']);
        }
    }
}
