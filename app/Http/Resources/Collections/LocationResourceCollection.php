<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\LocationResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.locations.index', [], false),
                    'title' => 'Lista de ubicaciones',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
