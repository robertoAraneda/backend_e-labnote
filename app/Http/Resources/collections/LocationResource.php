<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class LocationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.locations.show',
                        ['location' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
