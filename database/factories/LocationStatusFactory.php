<?php

namespace Database\Factories;

use App\Models\LocationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationStatusFactory extends Factory
{
    protected $model = LocationStatus::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->slug,
            'display' => $this->faker->text,
            'active' => $this->faker->boolean,
        ];
    }
}
