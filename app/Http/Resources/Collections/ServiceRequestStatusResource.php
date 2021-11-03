<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestStatusResource extends JsonResource
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
                        'api.service-request-statuses.show',
                        ['service_request_status' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}