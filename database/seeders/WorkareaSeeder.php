<?php

namespace Database\Seeders;

use App\Models\Workarea;
use Illuminate\Database\Seeder;

class WorkareaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Workarea::create([
            'name' => 'Biología Molecular',
            'active' => true,
        ]);
        Workarea::create([
            'name' => 'Bioquímica',
            'active' => true,
        ]);
        Workarea::create([
            'name' => 'Microbiología',
            'active' => true,
        ]);
        Workarea::create([
            'name' => 'Inmunología',
            'active' => true
        ]);
    }
}
