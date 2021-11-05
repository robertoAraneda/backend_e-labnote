<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Administrador',
            'Secretaria',
            'Tecnólogo Médico',
            'Developer',
            'super-admin'
        ];



        foreach ($roles as $role){
            Role::create([
                'name' => $role,
                'guard_name' => 'api',
                'created_user_id' => 1,
                'active' => true
            ]);
        }
        $user = User::find(1);

        $role = Role::where('name', 'Administrador')->first();

        $user->assignRole($role);
    }
}
