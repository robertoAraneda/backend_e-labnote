<?php

namespace Database\Factories;

use App\Models\Specimen;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecimenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Specimen::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition(): array
    {
        return [
            'accession_identifier' => $this->faker->randomNumber(8),
            'specimen_status_id' => 1,
            'specimen_code_id' => 1,
            'patient_id' => 1,
            'service_request_id' => 1,
        ];
    }
}
