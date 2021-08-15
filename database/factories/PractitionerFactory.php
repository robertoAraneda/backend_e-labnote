<?php

namespace Database\Factories;

use App\Models\AdministrativeGender;
use App\Models\Practitioner;
use Illuminate\Database\Eloquent\Factories\Factory;

class PractitionerFactory extends Factory
{
    protected $model = Practitioner::class;

    public function definition(): array
    {
        return [
            'given' => $this->faker->name,
            'family' => $this->faker->lastName,
            'rut' => $this->faker->slug,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'administrative_gender_id' => AdministrativeGender::factory(),
            'active' => $this->faker->boolean
        ];
    }
}
