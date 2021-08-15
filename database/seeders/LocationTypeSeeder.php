<?php

namespace Database\Seeders;

use App\Models\LocationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocationTypeSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            'HOSPITAL',
            'LABORATORIO CLINICO',
            'BANCO DE SANGRE',
            'UNIDAD DE RADIOLOGÍA',
            'TOMA DE MUESTRA',
        ];

        foreach ($array as $item)
            LocationType::create([
                'code' => 	Str::snake(Str::lower($item), '_'),
                'display' => $item,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);

    }
}
