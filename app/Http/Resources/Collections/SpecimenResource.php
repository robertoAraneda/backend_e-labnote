<?php

namespace App\Http\Resources\Collections;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecimenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'accession_identifier' => $this->accession_identifier,
            'collected_at' => Carbon::parse($this->collected_at)->format('d/m/Y h:i:s'),
            '_links' => [
                'self' => [
                    'href' => route('api.specimens.show', ['specimen' => $this->id], false),
                ],
            ],
        ];
    }
}
