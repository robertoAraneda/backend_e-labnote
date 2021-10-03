<?php

namespace Database\Seeders;

use App\Models\ServiceRequestIntent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceRequestIntentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serviceRequestIntents = [
            'PROPUESTA',
            'PLAN',
            'DIRECTIVA',
            'ORDEN',
            'ORDEN REFLEX',
        ];

        foreach ($serviceRequestIntents as $serviceRequestIntent)
            ServiceRequestIntent::create([
                'code' => 	Str::slug(Str::lower($serviceRequestIntent),'-'),
                'display' => 	$serviceRequestIntent,
                'active' => true,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
