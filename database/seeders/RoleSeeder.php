<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'Administrador', 'guard_name' => 'api']);
        Role::create(['name' => 'Secretaria', 'guard_name' => 'api']);
        Role::create(['name' => 'Tecnólogo Médico', 'guard_name' => 'api']);
    }
}
