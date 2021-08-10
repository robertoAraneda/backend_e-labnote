<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceRequestPriorityResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\ServiceRequestPriorityResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-priorities.index', [], false),
                    'title' => 'Lista de prioridades de ServiceRequest',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
