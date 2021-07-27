<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        State::create(['name' => 'REGIÓN DE ARICA Y PARINACOTA', 'code' => '15']);
        State::create(['name' => 'REGIÓN DE TARAPACÁ', 'code' => '01']);
        State::create(['name' => 'REGIÓN DE ANTOFAGASTA', 'code' => '02']);
        State::create(['name' => 'REGIÓN DE ATACAMA', 'code' => '03']);
        State::create(['name' => 'REGIÓN DE COQUIMBO', 'code' => '04']);
        State::create(['name' => 'REGIÓN DE VALPARAÍSO', 'code' => '05']);
        State::create(['name' => 'REGIÓN DEL LIBERTADOR GENERAL BERNARDO O\'HIGGINS', 'code' => '06']);
        State::create(['name' => 'REGIÓN DEL MAULE', 'code' => '07']);
        State::create(['name' => 'REGIÓN DE ÑUBLE', 'code' => '16']);
        State::create(['name' => 'REGIÓN DEL BIOBÍO', 'code' => '08']);
        State::create(['name' => 'REGIÓN DE LA ARAUCANÍA', 'code' => '09']);
        State::create(['name' => 'REGIÓN DE LOS RÍOS', 'code' => '14']);
        State::create(['name' => 'REGIÓN DE LOS LAGOS', 'code' => '10']);
        State::create(['name' => 'REGIÓN DE AYSÉN DEL GENERAL CARLOS IBÁÑEZ DEL CAMPO', 'code' => '11']);
        State::create(['name' => 'REGIÓN DE MAGALLANES Y DE LA ANTÁRTICA CHILENA', 'code' => '12']);
        State::create(['name' => 'REGIÓN METROPOLITANA DE SANTIAGO', 'code' => '13']);
    }
}
