<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationStatusResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'display' => $this->display,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.location-statuses.show',
                        ['location_status' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
