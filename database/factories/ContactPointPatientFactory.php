<?php

namespace Database\Factories;

use App\Models\ContactPointPatient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactPointPatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactPointPatient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'system' => 'mobile',
            'value' => $this->faker->phoneNumber,
            'use' => 'work'
        ];
    }
}
