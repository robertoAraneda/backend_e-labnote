<?php

namespace Database\Factories;

use App\Models\Analyte;
use App\Models\Disponibility;
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
            'slug' => $this->faker->slug,
            'clinical_information' => $this->faker->text,
            'loinc_id' => $this->faker->slug,
            'workarea_id' => Workarea::factory(),
            'availability_id' => Disponibility::factory(),
            'process_time_id' => ProcessTime::factory(),
            'medical_request_type_id' => MedicalRequestType::factory(),
            'created_user_id' => User::factory(),
            'is_patient_codable' => $this->faker->boolean,
            'active' => $this->faker->boolean
        ];
    }
}
