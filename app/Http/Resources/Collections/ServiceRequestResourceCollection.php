<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceRequestResourceCollection extends ResourceCollection
{

    public $collects = 'App\Http\Resources\Collections\ServiceRequestResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.service-requests.index', [], false),
                    'title' => 'Lista de solicitudes',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
