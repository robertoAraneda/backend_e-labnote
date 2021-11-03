<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalyteResource extends JsonResource
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
            'is_patient_codable' => (bool) $this->is_patient_codable,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.analytes.show', ['analyte' => $this->id], false),
                ] ,
            ],
        ];
    }
}
