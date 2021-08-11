<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationTypeResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\LocationTypeResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.location-types.index', [], false),
                    'title' => 'Lista de tipos de ubicaciones (procedencias)',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
