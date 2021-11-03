<?php

namespace Database\Seeders;

use App\Models\NobilisDocumentType;
use Illuminate\Database\Seeder;

class NobilisDocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $availabilities = [
            ['id' => 'CE', 'description' => 'CEDULA DE EXTRANJERO' ],
            ['id' => 'CI', 'description' => 'CEDULA DE IDENTIDAD' ],
            ['id' => 'DNI', 'description' => 'DOCUMENTO NACIONAL DE IDENTIDAD' ],
            ['id' => 'LC', 'description' => 'LIBRETA CIVICA' ],
            ['id' => 'NN', 'description' => 'INDOCUMENTADO' ],
            ['id' => 'PAS', 'description' => 'PASAPORTE' ],
            ['id' => 'RN', 'description' => 'RECIEN NACIDO' ],
            ['id' => 'RUT', 'description' => 'RUT' ],
            ['id' => 'VIH', 'description' => 'VIH' ],
        ];

        foreach ($availabilities as $availability)
            NobilisDocumentType::create([
                'id' => 	$availability['id'],
                'description' => $availability['description'],
            ]);
    }
}
