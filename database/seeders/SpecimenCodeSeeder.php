<?php

namespace Database\Seeders;

use App\Models\SpecimenCode;
use Illuminate\Database\Seeder;

class SpecimenCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specimenCodes = [
            'SANGRE TOTAL',
            'SANGRE ARTERIAL',
            'SANGRE VENOSA',
            'SANGRE CORDON',
            'ABSCESO',
            'ASPIRADO',
            'LIQUIDO AMNIÓTICO',
            'EXPECTORACIÓN',
        ];

        foreach ($specimenCodes as $item)
            SpecimenCode::create([
                'display' => 	$item,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
