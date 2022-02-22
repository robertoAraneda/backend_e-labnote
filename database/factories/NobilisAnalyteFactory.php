<?php

namespace Database\Factories;

use App\Models\NobilisAnalyte;
use Illuminate\Database\Eloquent\Factories\Factory;

class NobilisAnalyteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NobilisAnalyte::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => '302007',
            'description' => $this->faker->slug,
        ];
    }
}
