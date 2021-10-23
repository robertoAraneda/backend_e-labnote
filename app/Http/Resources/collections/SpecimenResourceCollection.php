<?php

namespace App\Http\Resources\collections;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SpecimenResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\SpecimenResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.specimens.index', [], false),
                    'title' => 'Lista de muestras',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
