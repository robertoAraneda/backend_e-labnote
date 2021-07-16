<?php

namespace Database\Factories;

use App\Models\Loinc;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoincFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loinc::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'loinc_num' => $this->faker->slug,
            'long_common_name' => $this->faker->title
        ];
    }
}
