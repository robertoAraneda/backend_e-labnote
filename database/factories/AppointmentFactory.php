<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Appointment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'appointment_status_id' => $this->faker->randomNumber(1),
            'service_request_id' => $this->faker->randomNumber(1),
            'patient_id' => $this->faker->randomNumber(1),
            'minutes_duration' => $this->faker->randomNumber(1),
            'start' => $this->faker->dateTime,
            'end' => $this->faker->dateTime,
            'description' => $this->faker->text(50),
        ];
    }

}
