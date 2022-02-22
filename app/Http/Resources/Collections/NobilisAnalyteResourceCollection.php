<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NobilisAnalyteResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\NobilisAnalyteResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.nobilis-analytes.index', [], false),
                    'title' => 'Lista de estados de exámenes Nobilis',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
