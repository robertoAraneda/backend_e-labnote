<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class PractitionerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'given' => $this->given,
            'family' => $this->family,
            'email' => $this->email,
            'rut' => $this->rut,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.practitioners.show',
                        ['practitioner' => $this->id],
                        false
                    ),
                ],
            ],
        ];
    }
}
