<?php

namespace Database\Seeders;

use App\Models\LocationPhysicalType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationPhysicalTypeSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            'EDIFICIO',
            'ALA',
            'PISO',
            'PASILLO',
            'SALA',
            'CAMA',
            'CASA',
            'GABINETE'
        ];

        foreach ($array as $item)
            LocationPhysicalType::create([
                'code' => 	Str::lower($item),
                'display' => $item,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);

    }
}
