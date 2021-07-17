<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SamplingConditionResourceCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\Collections\SamplingConditionResource';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.sampling-conditions.index', [], false),
                    'title' => 'Lista de condiciones para la toma de muestras',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
