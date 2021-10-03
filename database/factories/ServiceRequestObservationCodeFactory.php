<?php

namespace Database\Factories;

use App\Models\Analyte;
use App\Models\Availability;
use App\Models\Container;
use App\Models\Laboratory;
use App\Models\Location;
use App\Models\Loinc;
use App\Models\MedicalRequestType;
use App\Models\ProcessTime;
use App\Models\ServiceRequestObservationCode;
use App\Models\SpecimenCode;
use App\Models\Workarea;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestObservationCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ServiceRequestObservationCode::class;

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
            'slug' => $this->faker->slug,
            'container_id' => Container::factory(),
            'availability_id' => Availability::factory(),
            'laboratory_id' => Laboratory::factory(),
            'loinc_num' => Loinc::factory(),
            'specimen_code_id' => SpecimenCode::factory(),
            'analyte_id' => Analyte::factory(),
            'location_id' => Location::factory(),
            'process_time_id' => ProcessTime::factory(),
            'medical_request_type_id' => MedicalRequestType::factory(),
            'active' => $this->faker->boolean
        ];
    }
}
