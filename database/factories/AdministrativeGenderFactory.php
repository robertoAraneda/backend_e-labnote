<?php

namespace Database\Factories;

use App\Models\AdministrativeGender;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministrativeGenderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdministrativeGender::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'display' => $this->faker->name,
            'code' => $this->faker->title,
            'active'=> $this->faker->boolean
        ];
    }
}
