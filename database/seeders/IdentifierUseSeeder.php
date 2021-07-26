<?php

namespace Database\Seeders;

use App\Models\IdentifierUse;
use Illuminate\Database\Seeder;

class IdentifierUseSeeder extends Seeder
{
    private const IdentifierUse = [
        [
            'code' => 'usual',
            'display' => 'Usual'
        ],
        [
            'code' => 'official',
            'display' => 'Oficial'
        ],
        [
            'code' => 'temp',
            'display' => 'Temporal'
        ]
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::IdentifierUse as $identifier)
        {
            IdentifierUse::create(['code' => $identifier['code'], 'display' => $identifier['display']]);
        }

    }
}
