<?php

namespace Database\Seeders;

use App\Models\BusinessStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessStatusSeeder extends Seeder
{

    public function run()
    {
        $businesstStatuses = [
            'EN COLA TM',
            'EN ATENCIÓN TM',
            'MUESTRAS TOMADAS',
            'ESPERA DE RESULTADOS',
            'RESULTADOS ENTREGADOS',
        ];

        foreach ($businesstStatuses as $businessStatus)
            BusinessStatus::create([
                'code' => Str::slug(Str::lower($businessStatus), '-'),
                'display' => $businessStatus,
            ]);
    }
}
