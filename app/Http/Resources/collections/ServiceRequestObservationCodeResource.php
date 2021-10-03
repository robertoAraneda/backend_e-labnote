<?php

namespace App\Http\Resources\collections;

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
            'container' => $this->container->name,
            'container_id' => $this->container->id,
            'slug' => $this->slug,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-observation-codes.show', ['service_request_observation_code' => $this->id], false),
                ],
            ],
        ];
    }
}
