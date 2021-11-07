<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'requisition' => $this->requisition,
            'occurrence' => $this->occurrence,
            'is_confidential' => (boolean) $this->is_confidential,
            'diagnosis' => $this->diagnosis,
            'note' => $this->note,
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.service-requests.show',
                        ['service_request' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
