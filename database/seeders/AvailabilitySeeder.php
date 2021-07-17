<?php

namespace Database\Seeders;

use App\Models\Availability;
use Illuminate\Database\Seeder;

class AvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $availabilities = [
            'DISPONIBLE',
            'NO DISPONIBLE',
            'SOLO UPC HHHA',
            'DERIVADO',
            'DERIVADO EXTRASISTEMA',
            'SOLO UTM'
        ];

        foreach ($availabilities as $availability)
            Availability::create([
                'name' => 	$availability,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
