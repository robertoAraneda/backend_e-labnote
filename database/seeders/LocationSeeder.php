<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        Location::create([
            'location_status_id' => 1,
            'name' => 'UCI ADULTOS',
            'alias' => 'UCI',
            'description' => 'UCI ADULTOS',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

        Location::create([
            'location_status_id' => 1,
            'name' => 'UTI ADULTOS',
            'alias' => 'UTI',
            'description' => 'UTI ADULTOS',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);
        Location::create([
            'location_status_id' => 1,
            'name' => 'PEDIATRIA',
            'alias' => 'PEDIATRIA',
            'description' => 'PEDIATRIA',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);
        Location::create([
            'location_status_id' => 1,
            'name' => 'URGENCIA ADULTO',
            'alias' => 'URGENCIA ADULTO',
            'description' => 'URGENCIA ADULTO',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

        Location::create([
            'location_status_id' => 1,
            'name' => 'BIOQUIMICA',
            'alias' => 'BIOQUIMICA',
            'description' => 'BIOQUIMICA',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

        Location::create([
            'location_status_id' => 1,
            'name' => 'HEMATOLOGIA',
            'alias' => 'HEMATOLOGIA',
            'description' => 'HEMATOLOGIA',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

        Location::create([
            'location_status_id' => 1,
            'name' => 'INMUNOLOGIA',
            'alias' => 'INMUNOLOGIA',
            'description' => 'INMUNOLOGIA',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

        Location::create([
            'location_status_id' => 1,
            'name' => 'MICROBIOLOGIA',
            'alias' => 'MICROBIOLOGIA',
            'description' => 'MICROBIOLOGIA',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

        Location::create([
            'location_status_id' => 1,
            'name' => 'BIOLOGIA MOLECULAR',
            'alias' => 'BIOLOGIA MOLECULAR',
            'description' => 'BIOLOGIA MOLECULAR',
            'location_type_id' => 1,
            'location_physical_type_id' => 1,
        ]);

    }
}
