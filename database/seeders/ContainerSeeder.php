<?php

namespace Database\Seeders;

use App\Models\Container;
use Illuminate\Database\Seeder;

class ContainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $containers = [
            [
                'name' => 'TUBO CON ANTICOAGULANTE EDTA K2 (TAPA LILA) 3 mL',
                'shortname' => 'TAPA LILA',
                'color' => 'color'
            ],
            [
                'name' => 'TUBO CON ANTICOAGULANTE CITRATO DE SODIO 3,2% (TAPA CELESTE)',
                'shortname' => 'TAPA CELESTE',
                'color' => 'color'
            ],
            [
                'name' => 'TUBO SIN ANTICOAGULANTE CON GEL SEPARADOR',
                'shortname' => 'TAPA ROJA',
                'color' => 'color'
            ],
            [
                'name' => 'TUBO CON ANTICOAGULANTE FLUORURO DE SODIO (TAPA GRIS)',
                'shortname' => 'TAPA GRIS',
                'color' => 'color'
            ],
            [
                'name' => 'JERINGA HEPARINIZADA',
                'shortname' => 'GASES',
                'color' => 'color'
            ],
            [
                'name' => 'FRASCO TAPA ROSCA ESTERIL',
                'shortname' => 'TAPA ROSCA',
                'color' => 'color'
            ],
            [
                'name' => 'TÓRULA ESTÉRIL',
                'shortname' => 'TORULA',
                'color' => 'color'
            ],
            [
                'name' => 'CAJA DE POLIPROPILENO NEGRA, TAPA ROSCA, 30 ML',
                'shortname' => 'CAJA NEGRA',
                'color' => 'color'
            ],
        ];

        foreach ($containers as $key => $item)
            Container::create(array_merge($item,
                    [
                        'active' => true,
                        'created_user_id' => 1,
                        'created_user_ip' => '127.0.0.1'
                    ])
            );
    }
}
