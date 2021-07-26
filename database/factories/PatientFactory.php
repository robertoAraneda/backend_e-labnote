<?php

namespace Database\Factories;

use App\Models\AdministrativeGender;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Patient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'birthdate' => $this->faker->date(),
            'administrative_gender_id' => AdministrativeGender::factory(),
            'active'=> $this->faker->boolean
        ];
    }
}
