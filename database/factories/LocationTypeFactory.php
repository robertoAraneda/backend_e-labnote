<?php

namespace Database\Factories;

use App\Models\LocationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationTypeFactory extends Factory
{

    protected $model = LocationType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->slug,
            'display' => $this->faker->text,
            'active' => $this->faker->boolean,
        ];
    }
}
