<?php

namespace Database\Seeders;

use App\Models\SpecimenStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecimenStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specimenCodes = [
            'PENDIENTE',
            'DISPONIBLE',
            'NO DISPONIBLE',
            'INSATISFACTORIA',
            'ERROR',
        ];

        foreach ($specimenCodes as $item)
            SpecimenStatus::create([
                'code' => Str::slug( Str::lower($item), '-'),
                'display' => $item,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
