<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SamplingRoomPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'Administrador')->first();

        $user = User::find(1);
        $module = Module::where('slug', 'toma-de-muestras')->first();

        $permission = Permission::create([
            'name' => 'samplingRoom.index',
            'guard_name' => 'api',
            'model' => 'SamplingRoom',
            'action' => 'index',
            'description' => 'Acceso a módulo y gestión de tomas de muestra'
        ]);

        ModulePermission::create([
            'module_id' => $module->id,
            'permission_id' => $permission->id,
            'user_id' => $user->id,
        ]);

        $role->givePermissionTo('samplingRoom.index');
    }
}
