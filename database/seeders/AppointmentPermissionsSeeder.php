<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AppointmentPermissionsSeeder extends Seeder
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
                'name' => 'appointment.create',
                'action' => 'create',
                'description' => 'Crear agenda'
            ],
            [
                'name' => 'appointment.update',
                'action' => 'update',
                'description' => 'Modificar agenda'
            ],
            [
                'name' => 'appointment.delete',
                'action' => 'delete',
                'description' => 'Eliminar agenda'
            ],
            [
                'name' => 'appointment.show',
                'action' => 'show',
                'description' => 'Ver detalle de agenda'
            ],
            [
                'name' => 'appointment.index',
                'action' => 'index',
                'description' => 'Ver todos los agenda'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Appointment',
                'action' => $permission['action'],
                'description' => $permission['description'],
            ]);

            $role->givePermissionTo($permission['name']);
        }
    }
}
