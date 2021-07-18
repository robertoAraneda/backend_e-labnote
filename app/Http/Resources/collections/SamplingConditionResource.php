<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SamplingConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        if(isset($this->checkbox)){
            return [
                'id' => $this->id,
                'name' => $this->name,
                'active' => (bool)$this->active,
                'checkbox' =>(bool) $this->checkbox,
                '_links' => [
                    'self' => [
                        'href' => route('api.sampling-conditions.show', ['sampling_condition' => $this->id], false),
                    ],
                ],
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'active' => (bool)$this->active,
            '_links' => [
                'self' => [
                    'href' => route('api.sampling-conditions.show', ['sampling_condition' => $this->id], false),
                ],
            ],
        ];

    }
}
