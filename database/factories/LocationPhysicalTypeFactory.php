<?php

namespace Database\Factories;

use App\Models\LocationPhysicalType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationPhysicalTypeFactory extends Factory
{

    protected $model = LocationPhysicalType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->slug,
            'display' => $this->faker->text,
            'active' => $this->faker->boolean,
        ];
    }
}
