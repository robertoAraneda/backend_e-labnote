<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.laboratories.show', ['laboratory' => $this->id], false),
                ] ,
            ],
        ];
    }
}
