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
            'icon' => 'mdi-cog',
            'url' => 'roles',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);
        Menu::create([
            'name' => 'Permisos',
            'icon' => 'mdi-cog',
            'url' => 'permissions',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);
        Menu::create([
            'name' => 'Modulos',
            'icon' => 'mdi-cog',
            'url' => 'modules',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);
        Menu::create([
            'name' => 'Usuarios',
            'icon' => 'mdi-cog',
            'url' => 'users',
            'module_id' => Module::where('name', 'Configuración')->first()->id,
        ]);

        Menu::create([
            'name' => 'Buscar paciente',
            'icon' => 'mdi-cog',
            'url' => 'roles',
            'module_id' => Module::where('name', 'Solicitud de medios')->first()->id,
        ]);
        Menu::create([
            'name' => 'Crear solicitud normal',
            'icon' => 'mdi-cog',
            'url' => 'roles',
            'module_id' => Module::where('name', 'Solicitud de medios')->first()->id,
        ]);
        Menu::create([
            'name' => 'Crear solicitud VIH',
            'icon' => 'mdi-cog',
            'url' => 'roles',
            'module_id' => Module::where('name', 'Solicitud de medios')->first()->id,
        ]);
        Menu::create([
            'name' => 'Buscar paciente',
            'icon' => 'mdi-cog',
            'url' => 'roles',
            'module_id' => Module::where('name', 'Toma de muestras')->first()->id,
        ]);
        Menu::create([
            'name' => 'Activar paciente',
            'icon' => 'mdi-cog',
            'url' => 'roles',
            'module_id' => Module::where('name', 'Toma de muestras')->first()->id,
        ]);
    }
}
