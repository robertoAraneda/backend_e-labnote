<?php

namespace Database\Seeders;

use App\Models\ServiceRequestPriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceRequestPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serviceRequestPriorities = [
            'RUTINA',
            'URGENCIA',
            'STAT',
        ];

        foreach ($serviceRequestPriorities as $serviceRequestPriority)
            ServiceRequestPriority::create([
                'code' => 	Str::slug(Str::lower($serviceRequestPriority),'-'),
                'display' => 	$serviceRequestPriority,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
