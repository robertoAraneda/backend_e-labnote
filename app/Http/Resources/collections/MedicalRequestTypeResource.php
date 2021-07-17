<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRequestTypeResource extends JsonResource
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
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.medical-request-types.show', ['medical_request_type' => $this->id], false),
                ] ,
            ],
        ];
    }
}
