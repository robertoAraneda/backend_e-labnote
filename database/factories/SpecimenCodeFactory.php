<?php

namespace Database\Factories;

use App\Models\SpecimenCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecimenCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SpecimenCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'active' => $this->faker->boolean
        ];
    }
}
