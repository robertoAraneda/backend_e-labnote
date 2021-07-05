<?php

namespace Database\Factories;

use App\Models\ProcessTime;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcessTimeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcessTime::class;

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
