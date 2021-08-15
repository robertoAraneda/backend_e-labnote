<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceRequestIntentResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\ServiceRequestIntentResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-intents.index', [], false),
                    'title' => 'Lista de intenciones de ServiceRequest',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
