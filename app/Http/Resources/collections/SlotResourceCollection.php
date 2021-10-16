<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SlotResourceCollection extends ResourceCollection
{

    public $collects = 'App\Http\Resources\Collections\SlotResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.slots.index', [], false),
                    'title' => 'Lista de slots',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
