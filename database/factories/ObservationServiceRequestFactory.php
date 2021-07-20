<?php

namespace Database\Factories;

use App\Models\Analyte;
use App\Models\Availability;
use App\Models\Container;
use App\Models\Laboratory;
use App\Models\Loinc;
use App\Models\MedicalRequestType;
use App\Models\ObservationServiceRequest;
use App\Models\ProcessTime;
use App\Models\Specimen;
use App\Models\Workarea;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObservationServiceRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ObservationServiceRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {

        return [
            'clinical_information' => $this->faker->text,
            'name' => $this->faker->name,
            'container_id' => Container::factory(),
            'specimen_id' => Specimen::factory(),
            'availability_id' => Availability::factory(),
            'laboratory_id' => Laboratory::factory(),
            'loinc_num' => Loinc::factory(),
            'analyte_id' => Analyte::factory(),
            'workarea_id' => Workarea::factory(),
            'process_time_id' => ProcessTime::factory(),
            'medical_request_type_id' => MedicalRequestType::factory(),
            'active' => $this->faker->boolean
        ];
    }
}
