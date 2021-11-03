<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestCategoryResource extends JsonResource
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
                        'api.service-request-categories.show',
                        ['service_request_category' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
