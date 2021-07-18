<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Module;
use App\Models\Permission;
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

/*        $permission = Permission::factory()->create();
        $module = Module::factory()->create();*/
        return [
            'module_id' => Module::factory(),
            'permission_id' => Permission::factory(),
            'name' => $this->faker->title(),
            'icon' => $this->faker->lastName,
            'url' => $this->faker->url,
            'order' => $this->faker->numberBetween(1,1000),
            'active' => $this->faker->boolean,
        ];
    }
}
