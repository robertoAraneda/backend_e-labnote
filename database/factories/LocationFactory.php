<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\LocationPhysicalType;
use App\Models\LocationStatus;
use App\Models\LocationType;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Location::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'location_status_id' => LocationStatus::factory(),
            'name' => $this->faker->name,
            'alias' => $this->faker->text,
            'description' => $this->faker->text(100),
            'location_type_id' => LocationType::factory(),
            'location_physical_type_id' => LocationPhysicalType::factory(),
            //'part_of_location_id' => Location::factory(),
            'managing_organization_id' => Organization::factory(),

        ];
    }
}
