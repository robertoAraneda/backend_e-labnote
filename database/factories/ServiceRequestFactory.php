<?php

namespace Database\Factories;

use App\Models\AddressPatient;
use App\Models\AdministrativeGender;
use App\Models\ContactPatient;
use App\Models\ContactPointPatient;
use App\Models\HumanName;
use App\Models\IdentifierPatient;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestIntent;
use App\Models\ServiceRequestPriority;
use App\Models\ServiceRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceRequestFactory extends Factory
{
    protected $model = ServiceRequest::class;

    public function definition(): array
    {
        return [
            'requisition' => $this->faker->creditCardNumber,
            //'occurrence' => $this->faker->dateTime,
            'note' => $this->faker->text,
            'service_request_status_id' => ServiceRequestStatus::factory(),
            'service_request_intent_id' => ServiceRequestIntent::factory(),
            'service_request_priority_id' => ServiceRequestPriority::factory(),
            'service_request_category_id' => ServiceRequestCategory::factory(),
            'patient_id' => Patient::factory()
                ->has(AdministrativeGender::factory())
                ->has(IdentifierPatient::factory())
                ->has(HumanName::factory())
                ->has(AddressPatient::factory())
                ->has(ContactPointPatient::factory())
                ->has(ContactPatient::factory()),
            'requester_id' => Practitioner::factory(),
            'performer_id' => Practitioner::factory(),
            'location_id' => Location::factory(),
        ];
    }
}
