<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkareaResource extends JsonResource
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
            'active' => (bool) $this->active,
            '_link' => [
                'self' =>[
                    'href' => route('api.workareas.show', ['workarea' => $this->id], false),
                ] ,
            ],
        ];
    }
}
