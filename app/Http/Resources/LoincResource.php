<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoincResource extends JsonResource
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
            'loinc_num' => $this->loinc_num,
            'long_common_name' => $this->long_common_name,
            '_links' => [
                'self' => [
                    'href' => route('api.loincs.show', ['loinc' => $this->loinc_num], false),
                ],
            ],
        ];
    }
}
