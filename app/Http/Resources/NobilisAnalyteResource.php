<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NobilisAnalyteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route(
                        'api.nobilis-analytes.show',
                        ['nobilis_analyte' => $this->id],
                        false),
                ],
            ],
        ];
    }

    private function date($date): ?string
    {
        if(!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }
}
