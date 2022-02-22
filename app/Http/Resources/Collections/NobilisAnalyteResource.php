<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class NobilisAnalyteResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            '_links' => [
                'self' => [
                    'href' => route('api.nobilis-analytes.show', ['nobilis_analyte' => $this->id], false),
                ],
            ],
        ];
    }
}
