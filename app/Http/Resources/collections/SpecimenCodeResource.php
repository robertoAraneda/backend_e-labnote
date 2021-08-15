<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecimenCodeResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.specimen-codes.show', ['specimen_code' => $this->id], false),
                ],
            ],
        ];
    }
}
