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
            ['name' => 'Configuración',
                'icon' => 'mdi-newspaper-variant-outline',
                'url' => 'settings',
                'slug' => 'configuracion'
            ]);

        Module::create(
            ['name' => 'Solicitud de medios',
                'icon' => 'mdi-cog',
                'url' => 'serviceRequest',
                'slug' => 'solicitud-de-medios'
            ]);

        Module::create(
            ['name' => 'Toma de muestras',
                'icon' => 'mdi-opacity',
                'url' => 'sampling',
                'slug' => 'toma-de-muestras'
            ]);

        Module::create(
            ['name' => 'Configuración avanzada',
                'icon' => 'mdi-cogs',
                'url' => 'advancedSettings',
                'slug' => 'configuracion-avanzada'
            ]);

    }
}
