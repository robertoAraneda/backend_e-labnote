<?php

namespace Database\Factories;

use App\Models\Slot;
use Illuminate\Database\Eloquent\Factories\Factory;

class SlotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Slot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'slot_status_id' => $this->faker->randomNumber(1),
            'start' => $this->faker->dateTime,
            'end' => $this->faker->dateTime,
            'overbooked' => false,
            'comment' => $this->faker->text(50),
        ];
    }
}
