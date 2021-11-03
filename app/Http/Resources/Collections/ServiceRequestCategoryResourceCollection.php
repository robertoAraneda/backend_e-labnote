<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceRequestCategoryResourceCollection extends ResourceCollection
{

    public $collects = 'App\Http\Resources\Collections\ServiceRequestCategoryResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.service-request-categories.index', [], false),
                    'title' => 'Lista de categorias de ServiceRequest',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
