<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContainerResource extends JsonResource
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
            'shortname' => $this->shortname,
            'color' => $this->color,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.containers.show', ['container' => $this->id], false),
                ] ,
            ],
        ];
    }
}
