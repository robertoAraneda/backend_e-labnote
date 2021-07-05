<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class DisponibilityPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'disponibility.create',
            'disponibility.update',
            'disponibility.delete',
            'disponibility.show',
            'disponibility.index'
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);

        }
    }
}
