<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentStatusResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'display' => $this->display,
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.appointment-statuses.show',
                        ['appointment_status' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
