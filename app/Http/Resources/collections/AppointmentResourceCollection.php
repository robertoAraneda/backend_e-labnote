<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AppointmentResourceCollection extends ResourceCollection
{

    public $collects = 'App\Http\Resources\Collections\AppointmentResource';

    public function toArray($request): array
    {
        return [
            '_links' => [
                'self' => [
                    'href' => route('api.appointments.index', [], false),
                    'title' => 'Lista de agendas',
                ]
            ],
            'count' => $this->collection->count(),
            'collection' => $this->collection
        ];
    }
}
