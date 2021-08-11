<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationPhysicalTypeResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\LocationPhysicalTypeResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.location-physical-types.index', [], false),
                    'title' => 'Lista de tipos de ubicación física (salas)',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
