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
        Module::create(
            [   'name' => 'Configuración',
                'icon' => 'cog',
                'url' => 'setting',
                'slug' => 'configuracion'
            ]);
        Module::create(['name' => 'Solicitud de medios',
            'icon' => 'cog',
            'url' => 'laboratoryRequest',
             'slug' => 'solicitud-de-medios'
            ]);
        Module::create(['name' => 'Toma de muestras',
            'icon' => 'cog',
            'url' => 'sampling',
            'slug' => 'toma-de-muestras'
            ]);
    }
}
