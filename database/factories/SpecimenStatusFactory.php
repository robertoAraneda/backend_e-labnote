<?php

namespace Database\Factories;

use App\Models\SpecimenStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecimenStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SpecimenStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->slug,
            'display' => $this->faker->text,
            'active' => $this->faker->boolean
        ];
    }
}
