<?php

namespace Database\Factories;

use App\Models\SampleType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SampleTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SampleType::class;

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
