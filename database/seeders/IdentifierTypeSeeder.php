<?php

namespace Database\Seeders;

use App\Models\IdentifierType;
use Illuminate\Database\Seeder;

class IdentifierTypeSeeder extends Seeder
{

   private const IdentifierType = [
        [
            'code' => 'run',
            'display' => 'RUN'
        ],
        [
            'code' => 'passport-number',
            'display' => 'PASAPORTE'
        ],
        [
            'code' => 'confidential',
            'display' => 'CONFIDENCIAL'
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
