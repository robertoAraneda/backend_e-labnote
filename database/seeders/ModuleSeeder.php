<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::create(['name' => 'Configuración']);
        Module::create(['name' => 'Solicitud de medios']);
        Module::create(['name' => 'Toma de muestras']);
    }
}
