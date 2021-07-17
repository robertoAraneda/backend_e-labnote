<?php

namespace Database\Factories;

use App\Models\Analyte;
use App\Models\Availability;
use App\Models\MedicalRequestType;
use App\Models\ProcessTime;
use App\Models\User;
use App\Models\Workarea;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalyteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Analyte::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'is_patient_codable' => $this->faker->boolean,
            'active' => $this->faker->boolean
        ];
    }
}
