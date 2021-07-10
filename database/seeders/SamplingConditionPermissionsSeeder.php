<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SamplingConditionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'samplingCondition.create',
            'samplingCondition.update',
            'samplingCondition.delete',
            'samplingCondition.show',
            'samplingCondition.index'
        ];

        foreach ($permissions as $permission){
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api'
            ]);

        }
    }
}
