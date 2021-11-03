<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'birthdate' => $this->birthdate,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.patients.show', ['patient' => $this->id], false),
                ] ,
            ],
        ];
    }
}
