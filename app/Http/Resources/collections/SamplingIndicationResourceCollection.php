<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SamplingIndicationResourceCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\Collections\SampleTypeResource';


    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.sampling-indications.index', [], false),
                    'title' => 'Lista de indicaciones toma de muestra',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
