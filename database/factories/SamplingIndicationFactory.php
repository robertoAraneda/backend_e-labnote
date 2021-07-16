<?php

namespace Database\Factories;

use App\Models\SamplingIndication;
use Illuminate\Database\Eloquent\Factories\Factory;

class SamplingIndicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SamplingIndication::class;

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
