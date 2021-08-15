<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestPriorityResource extends JsonResource
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
                        'api.service-request-priorities.show',
                        ['service_request_priority' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
