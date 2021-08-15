<?php

namespace Database\Factories;

use App\Models\ServiceRequestCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceRequestCategory::class;

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
