<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationStatusResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\LocationStatusResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.location-statuses.index', [], false),
                    'title' => 'Lista de estados de ubicaciones (procedencias)',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
