<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'rut' => $this->rut,
            'names' => $this->names,
            'lastname' => $this->lastname,
            'mother_lastname' => $this->mother_lastname,
            'email' => $this->email,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.users.show', ['user' => $this->id], false),
                ] ,
            ],
        ];
    }
}
