<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'state_code' => $this->state->code,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.cities.show', ['city' => $this->code], false),
                ] ,
            ],
        ];
    }
}
