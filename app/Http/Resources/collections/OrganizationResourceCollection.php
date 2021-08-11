<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrganizationResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\OrganizationResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.organizations.index', [], false),
                    'title' => 'Lista de organizaciones',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
