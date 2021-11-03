<?php

namespace App\Http\Resources\Collections;

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
            'display' => $this->display,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.specimen-codes.show', ['specimen_code' => $this->id], false),
                ],
            ],
        ];
    }
}
