<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceRequestStatusResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\ServiceRequestStatusResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-statuses.index', [], false),
                    'title' => 'Lista de estados de ServiceRequest',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
