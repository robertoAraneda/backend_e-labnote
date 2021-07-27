<?php

namespace Database\Factories;

use App\Models\AddressPatient;
use App\Models\City;
use App\Models\District;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressPatientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AddressPatient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'use' => 'home',
            'text' => $this->faker->streetAddress,
            'city_code' => City::factory(),
            'state_code' => State::factory()
        ];
    }
}
