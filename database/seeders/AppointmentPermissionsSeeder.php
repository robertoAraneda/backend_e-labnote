<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
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
            [
                'name' => 'schedule.index',
                'action' => 'index',
                'description' => 'Ver calendario de agenda'
            ],
        ];

        $role = Role::where('name', 'Administrador')->first();
        $user = User::find(1);
        $module = Module::where('slug', 'agenda')->first();

        foreach ($permissions as $permission) {
            $permission = Permission::create([
                'name' => $permission['name'],
                'guard_name' => 'api',
                'model' => 'Appointment',
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
