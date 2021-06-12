<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'rut' => $this->rut,
            'names' => $this->names,
            'lastname' => $this->lastname,
            'mother_lastname' => $this->mother_lastname,
            'email' => $this->email,
        ];
    }
}
