<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'code' => $this->faker->title,
            'state_code' => State::factory(),
            'active'=> $this->faker->boolean
        ];
    }
}
