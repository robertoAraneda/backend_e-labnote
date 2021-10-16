<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AppointmentStatusResourceCollection extends ResourceCollection
{
    public $collects = 'App\Http\Resources\Collections\AppointmentStatusResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.appointment-statuses.index', [], false),
                    'title' => 'Lista de estados de agenda',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
