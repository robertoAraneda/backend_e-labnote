<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentTypeResource extends JsonResource
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
                        'api.appointment-types.show',
                        ['appointment_type' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
