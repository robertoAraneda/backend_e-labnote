<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SamplingIndicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
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
                    'href' => route('api.sampling-indications.show', ['sampling_indication' => $this->id], false),
                ],
            ],
        ];
    }
}
