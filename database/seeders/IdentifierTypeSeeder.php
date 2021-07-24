<?php

namespace Database\Seeders;

use App\Models\IdentifierType;
use Illuminate\Database\Seeder;

class IdentifierTypeSeeder extends Seeder
{

   private const IdentifierType = [
        [
            'code' => 'RUT',
            'display' => 'Número de RUT'
        ],
        [
            'code' => 'PASAPORTE',
            'display' => 'Número de pasaporte'
        ],
        [
            'code' => 'FICHA CLINICA',
            'display' => 'Número de ficha clínica'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::IdentifierType as $identifier)
        {
            IdentifierType::create(['code' => $identifier['code'], 'display' => $identifier['display']]);
        }

    }
}
