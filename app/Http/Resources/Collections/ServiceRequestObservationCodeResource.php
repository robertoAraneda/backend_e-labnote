<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestObservationCodeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'loinc_num' => $this->loinc_num,
            'specimen_code' => $this->specimenCode->display,
            'specimen_id' => $this->specimenCode->id,
            'core_name' => $this->analyte->name,
            'isConfidential' => (bool) $this->analyte->is_patient_codable,
            'container' => $this->container->name,
            'container_id' => $this->container->id,
            'integration' => $this->integration($this->nobilis),
            'slug' => $this->slug,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-observation-codes.show', ['service_request_observation_code' => $this->id], false),
                ],
            ],
        ];
    }

    private function integration($payload){
        if(!isset($payload)) return null;

        return [
            'lis' => $payload->lis_name,
            'observation_service_request' =>
                [
                    'id' => $payload->serviceRequestObservationCode->id,
                    'name' => $payload->serviceRequestObservationCode->name,
                    'loinc_num' => $payload->serviceRequestObservationCode->loinc_num,
                    '_links' => [
                        'self' => [
                            'href' => route('api.service-request-observation-codes.show', ['service_request_observation_code' => $payload->serviceRequestObservationCode->id], false)
                        ]
                    ]
                ],
            'nobilis' =>
                [
                    'id' => $payload->nobilis->id,
                    'description' => $payload->nobilis->description,
                    '_links' => [
                        'self' => [
                            'href' => route('api.nobilis-analytes.show', ['nobilis_analyte' => $payload->nobilis->id], false)
                        ]
                    ]
                ],
        ];

    }
}
