<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SampleQuantityResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.sample-quantities.index', [], false),
                    'title' => 'Lista de cantidad de muestra',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
