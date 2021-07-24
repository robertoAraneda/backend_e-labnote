<?php

namespace Database\Factories;

use App\Models\IdentifierType;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdentifierTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = IdentifierType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'use' => $this->faker->languageCode,
            'display' => $this->faker->name,
        ];
    }
}
