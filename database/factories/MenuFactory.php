<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'module_id' => Module::factory(),
            'name' => $this->faker->title(),
            'status' => $this->faker->numberBetween(0,1),
        ];
    }
}
