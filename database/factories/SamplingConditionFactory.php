<?php

namespace Database\Factories;

use App\Models\SamplingCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

class SamplingConditionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SamplingCondition::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'active' => $this->faker->boolean
        ];
    }
}
