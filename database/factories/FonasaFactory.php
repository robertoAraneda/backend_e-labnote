<?php

namespace Database\Factories;

use App\Models\Fonasa;
use Illuminate\Database\Eloquent\Factories\Factory;

class FonasaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Fonasa::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'mai_code' => $this->faker->word,
            'rem_code' => $this->faker->languageCode,
            'active' => $this->faker->boolean
        ];
    }
}
