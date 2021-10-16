<?php

namespace Database\Seeders;

use App\Models\AppointmentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppointmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointmentStatuses = [
            'CHECKUP',
            'EMERGENCY',
            'FOLLOWUP',
            'ROUTINE',
            'WALKING',
        ];

        foreach ($appointmentStatuses as $appointmentStatus)
            AppointmentType::create([
                'code' => Str::slug(Str::lower($appointmentStatus), '-'),
                'display' => $appointmentStatus,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
