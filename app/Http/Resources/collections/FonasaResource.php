<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FonasaResource extends JsonResource
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
            'name' => $this->name,
            'rem_code' => $this->rem_code,
            'mai_code' =>  $this->mai_code,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.fonasas.show', ['fonasa' => $this->mai_code], false),
                ] ,
            ],
        ];
    }
}
