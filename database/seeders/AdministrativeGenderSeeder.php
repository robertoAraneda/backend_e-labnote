<?php

namespace Database\Seeders;

use App\Models\AdministrativeGender;
use Illuminate\Database\Seeder;

class AdministrativeGenderSeeder extends Seeder
{
   private const AdministrativeGender = [
        [
            'code' => 'male',
            'display' => 'MASCULINO'
        ],
        [
            'code' => 'female',
            'display' => 'FEMENINO'
        ],
        [
            'code' => 'other',
            'display' => 'OTRO'
        ],
        [
            'code' => 'unknown',
            'display' => 'DESCONOCIDO'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::AdministrativeGender as $identifier)
        {
            AdministrativeGender::create(['code' => $identifier['code'], 'display' => $identifier['display']]);
        }

    }
}
