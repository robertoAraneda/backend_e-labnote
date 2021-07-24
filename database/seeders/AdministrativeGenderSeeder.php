<?php

namespace Database\Seeders;

use App\Models\AdministrativeGender;
use Illuminate\Database\Seeder;

class AdministrativeGenderSeeder extends Seeder
{
   private const AdministrativeGender = [
        [
            'code' => 'male',
            'display' => 'Masculino'
        ],
        [
            'code' => 'female',
            'display' => 'Femenino'
        ],
        [
            'code' => 'other',
            'display' => 'Otro'
        ],
        [
            'code' => 'unknown',
            'display' => 'Desconocido'
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
