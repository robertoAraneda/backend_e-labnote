<?php

namespace Database\Seeders;

use App\Models\ServiceRequestCategory;
use Illuminate\Database\Seeder;

class ServiceRequestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiceRequestCategory::create([
            'code' => '108252007',
            'display' => 'LABORATORY PROCEDURE',
            'active' => true,
            'created_user_id' => 1,
            'created_user_ip' => '127.0.0.1'
        ]);

        ServiceRequestCategory::create([
            'code' => '363679005',
            'display' => 'IMAGING',
            'active' => true,
            'created_user_id' => 1,
            'created_user_ip' => '127.0.0.1'
        ]);
    }
}
