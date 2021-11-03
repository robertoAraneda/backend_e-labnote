<?php

namespace App\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AppointmentTypeResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\AppointmentTypeResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.appointment-types.index', [], false),
                    'title' => 'Lista de tipos de agenda',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
