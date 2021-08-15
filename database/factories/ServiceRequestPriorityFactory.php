<?php

namespace Database\Factories;

use App\Models\ServiceRequestPriority;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestPriorityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceRequestPriority::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->slug,
            'display' => $this->faker->title,
            'active' => $this->faker->boolean,
        ];
    }
}
