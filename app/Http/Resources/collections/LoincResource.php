<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoincResource extends JsonResource
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
            'loinc_num' => $this->loinc_num,
            'long_common_name' => $this->long_common_name,
            '_links' => [
                'self' =>[
                    'href' => route('api.loinc.show', ['loinc' => $this->loinc_num], false),
                ] ,
            ],
        ];
    }
}
