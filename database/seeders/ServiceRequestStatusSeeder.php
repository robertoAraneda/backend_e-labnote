<?php

namespace Database\Seeders;

use App\Models\ServiceRequestStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceRequestStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serviceRequestStatuses = [
            'BORRADOR',
            'ACTIVO',
            'SUSPENDIDO',
            'RECHAZADO',
            'COMPLETO',
            'INGRESADO CON ERROR',
        ];

        foreach ($serviceRequestStatuses as $serviceRequestStatus)
            ServiceRequestStatus::create([
                'code' => 	Str::slug(Str::lower($serviceRequestStatus),'-'),
                'display' => 	$serviceRequestStatus,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
