<?php

namespace Database\Seeders;

use App\Models\ProcessTime;
use Illuminate\Database\Seeder;

class ProcessTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $availabilities = [
            'HABIL',
            'TURNO',
            'SOLO JUEVES',
            'MARTES Y JUEVES',
            'CADA 15 DIAS',
            'HABIL-TURNO'
        ];

        foreach ($availabilities as $availability)
            ProcessTime::create([
                'name' => 	$availability,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
