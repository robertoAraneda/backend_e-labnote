<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{

    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->slug,
            'alias' => $this->faker->text,
            'active' => $this->faker->boolean,
        ];
    }
}