<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdministrativeGenderResource extends JsonResource
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
            'display' => $this->display,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.administrative-genders.show', ['administrative_gender' => $this->id], false),
                ] ,
            ],
        ];
    }
}