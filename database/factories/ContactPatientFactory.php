<?php

namespace Database\Factories;

use App\Models\ContactPatient;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactPatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ContactPatient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'given' => $this->faker->name,
            'family' => $this->faker->lastname,
            'relationship' => 'Madre',
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email
        ];
    }
}
