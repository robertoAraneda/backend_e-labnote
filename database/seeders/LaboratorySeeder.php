<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use App\Models\LaboratoryModule;
use App\Models\Module;
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
            'email' => 'laboratorio@asur.cl',
            'laboratory_information_system_id' => null
        ]);
        Laboratory::create([
            'name' => 'Laboratorio Hospital HHHA',
            'address' => 'Montt 115',
            'phone' => '+5699876565',
            'redirect' => 'http://hhha.elabnote.cl',
            'email' => 'hhha@asur.cl',
            'laboratory_information_system_id' => null
        ]);

        Laboratory::create([
            'name' => 'E-labnote',
            'address' => 'Cloud',
            'phone' => '+56958639620',
            'redirect' => 'http://elabnote.cl',
            'email' => 'soporte@elabnote.cl',
            'laboratory_information_system_id' => null
        ]);

        $modules = Module::all();

        foreach ($modules as $key => $module){
            LaboratoryModule::create([
                'laboratory_id' => Laboratory::where('email', 'laboratorio@asur.cl')->first()->id,
                'module_id' => $module->id,
                'user_id' => 1
            ]);

            LaboratoryModule::create([
                'laboratory_id' => Laboratory::where('email', 'soporte@elabnote.cl')->first()->id,
                'module_id' => $module->id,
                'user_id' => 1
            ]);
        }
    }
}
