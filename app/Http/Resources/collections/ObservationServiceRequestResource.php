<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObservationServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name($this->analyte, $this->specimen),
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.observation-service-requests.show', ['observation_service_request' => $this->id], false),
                ],
            ],
        ];
    }

    private function name($analyte, $specimen): string
    {
        if(isset($analyte) && isset($specimen)) return $analyte->name.", ".$specimen->name;
        return '';
    }
}
