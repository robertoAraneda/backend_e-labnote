<?php

namespace Database\Seeders;

use App\Models\AppointmentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointmentStatuses = [
            'PROPOSED',
            'PENDING',
            'BOOKED',
            'ARRIVED',
            'FULFILLED',
            'CANCELLED',
            'NOSHOW',
            'ENTERED IN ERROR',
            'CHECKED IN',
            'WAITLIST',
        ];

        foreach ($appointmentStatuses as $appointmentStatus)
            AppointmentStatus::create([
                'code' => Str::slug(Str::lower($appointmentStatus), '-'),
                'display' => $appointmentStatus,
                'created_user_id' => 1,
                'created_user_ip' => '127.0.0.1'
            ]);
    }
}
