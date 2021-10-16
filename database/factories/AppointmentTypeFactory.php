<?php

namespace Database\Factories;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentTypeFactory extends Factory
{

    protected $model = AppointmentType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->slug,
            'display' => $this->faker->title,
        ];
    }
}
