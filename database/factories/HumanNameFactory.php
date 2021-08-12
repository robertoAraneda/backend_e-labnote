<?php

namespace Database\Factories;

use App\Models\HumanName;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class HumanNameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HumanName::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {

        return [
            'use' => 'official',
            'given' => $this->faker->name,
            'father_family' => $this->faker->lastName,
            'mother_family' => $this->faker->lastName
        ];
    }
}
