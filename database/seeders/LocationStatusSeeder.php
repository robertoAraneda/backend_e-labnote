<?php

namespace Database\Seeders;

use App\Models\LocationStatus;
use Illuminate\Database\Seeder;

class LocationStatusSeeder extends Seeder
{

    public function run(): void
    {
        $array = [
            'ACTIVO',
            'SUSPENDIDO',
            'INACTIVO'
        ];

        foreach ($array as $item)
            LocationStatus::create([
                'code' => 	$item,
                'display' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);

    }
}
