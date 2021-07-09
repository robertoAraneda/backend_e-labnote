<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SampleQuantityPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'sampleQuantity.create',
            'sampleQuantity.update',
            'sampleQuantity.delete',
            'sampleQuantity.show',
            'sampleQuantity.index'
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);

        }
    }
}
