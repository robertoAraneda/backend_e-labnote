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
                'slug' => 'configuracion',
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1',
                'updated_user_id' => 1,
                'updated_user_ip' => '127.0.0.1',
            ]);

        Module::create(
            ['name' => 'Solicitud de medios',
                'icon' => 'mdi-cog',
                'url' => 'serviceRequest',
                'slug' => 'solicitud-de-medios',
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1',
                'updated_user_id' => 1,
                'updated_user_ip' => '127.0.0.1',
            ]);

        Module::create(
            ['name' => 'Toma de muestras',
                'icon' => 'mdi-opacity',
                'url' => 'sampling',
                'slug' => 'toma-de-muestras',
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1',
                'updated_user_id' => 1,
                'updated_user_ip' => '127.0.0.1',
            ]);

        Module::create(
            ['name' => 'Configuración avanzada',
                'icon' => 'mdi-cogs',
                'url' => 'advancedSettings',
                'slug' => 'configuracion-avanzada',
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1',
                'updated_user_id' => 1,
                'updated_user_ip' => '127.0.0.1',
            ]);

        Module::create(
            ['name' => 'Agenda',
                'icon' => 'mdi-cogs',
                'url' => 'appointments',
                'slug' => 'agenda',
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1',
                'updated_user_id' => 1,
                'updated_user_ip' => '127.0.0.1',
            ]);
    }
}
