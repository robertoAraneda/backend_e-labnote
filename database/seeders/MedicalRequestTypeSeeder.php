<?php

namespace Database\Seeders;

use App\Models\MedicalRequestType;
use Illuminate\Database\Seeder;

class MedicalRequestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $medicalRequestTypes = [
            'SOLICITUD NORMAL',
            'SOLICITUD MEDICA MICROBIOLOGIA',
            'SOLICITUD MEDICA TBC',
            'FORMULARIO IRAG Y 2019-nCoV'
        ];

        foreach ($medicalRequestTypes as $medicalRequestType)
        MedicalRequestType::create([
            'name' => 	$medicalRequestType,
            'active' => true,
            'created_user_id' => 1,
            'created_user_ip' => '127.0.0.1'
        ]);
    }
}
