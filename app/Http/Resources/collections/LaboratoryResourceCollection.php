<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LaboratoryResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.laboratories.index', [], false),
                    'title' => 'Lista de laboratorios',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
