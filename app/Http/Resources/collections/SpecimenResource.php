<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecimenResource extends JsonResource
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
                    'href' => route('api.specimens.show', ['specimen' => $this->id], false),
                ],
            ],
        ];
    }
}
