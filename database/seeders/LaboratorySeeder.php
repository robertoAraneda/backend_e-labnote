<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use Illuminate\Database\Seeder;

class LaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Laboratory::create([
            'name' => 'Laboratorio Inmunológico del Sur',
            'address' => 'Los pablos 999',
            'phone' => '+5699876565',
            'redirect' => 'http://labisur.elabnote.cl',
            'email' => 'laboratorio@asur.cl'
        ]);
        Laboratory::create([
            'name' => 'Laboratorio Hospital HHHA',
            'address' => 'Montt 115',
            'phone' => '+5699876565',
            'redirect' => 'http://hhha.elabnote.cl',
            'email' => 'hhha@asur.cl'
        ]);
    }
}
