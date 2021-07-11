<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'active' => (bool) $this->active,
            '_link' => [
                'self' =>[
                    'href' => route('api.laboratories.show', ['laboratory' => $this->id], false),
                ] ,
            ],
        ];
    }
}
