<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PractitionerResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\PractitionerResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.practitioners.index', [], false),
                    'title' => 'Lista de profesionales',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
