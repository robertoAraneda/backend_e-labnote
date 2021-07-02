<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Menu::create([
            'name' => 'Roles',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);
        Menu::create([
            'name' => 'Permisos',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);
        Menu::create([
            'name' => 'Modulos',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);
        Menu::create([
            'name' => 'Usuarios',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);

        Menu::create([
            'name' => 'Buscar paciente',
            'module_id' => Module::where('name', 'Solicitud de medios')->first()->id,
        ]);
        Menu::create([
            'name' => 'Crear solicitud normal',
            'module_id' => Module::where('name', 'Solicitud de medios')->first()->id,
        ]);
        Menu::create([
            'name' => 'Crear solicitud VIH',
            'module_id' => Module::where('name', 'Solicitud de medios')->first()->id,
        ]);
        Menu::create([
            'name' => 'Buscar paciente',
            'module_id' => Module::where('name', 'Toma de muestras')->first()->id,
        ]);
        Menu::create([
            'name' => 'Activar paciente',
            'module_id' => Module::where('name', 'Toma de muestras')->first()->id,
        ]);
    }
}
