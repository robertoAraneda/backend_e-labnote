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
                'color' => 'purple',
                'suffix' => 'A01'
            ],
            [
                'name' => 'TUBO CON ANTICOAGULANTE CITRATO DE SODIO 3,2% (TAPA CELESTE)',
                'shortname' => 'TAPA CELESTE',
                'color' => 'blue',
                'suffix' => 'A02'
            ],
            [
                'name' => 'TUBO SIN ANTICOAGULANTE CON GEL SEPARADOR',
                'shortname' => 'TAPA ROJA',
                'color' => 'red',
                'suffix' => 'A03'
            ],
            [
                'name' => 'TUBO CON ANTICOAGULANTE FLUORURO DE SODIO (TAPA GRIS)',
                'shortname' => 'TAPA GRIS',
                'color' => 'grey',
                'suffix' => 'A04'
            ],
            [
                'name' => 'JERINGA HEPARINIZADA',
                'shortname' => 'GASES',
                'color' => 'color',
                'suffix' => 'A05'
            ],
            [
                'name' => 'FRASCO TAPA ROSCA ESTERIL',
                'shortname' => 'TAPA ROSCA',
                'color' => 'green',
                'suffix' => 'A06'
            ],
            [
                'name' => 'TÓRULA ESTÉRIL',
                'shortname' => 'TORULA',
                'color' => 'black',
                'suffix' => 'A07'
            ],
            [
                'name' => 'CAJA DE POLIPROPILENO NEGRA, TAPA ROSCA, 30 ML',
                'shortname' => 'CAJA NEGRA',
                'color' => 'black',
                'suffix' => 'A08'
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
