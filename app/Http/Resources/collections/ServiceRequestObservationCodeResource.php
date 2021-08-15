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
