<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class SpecimenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'accession_identifier' => $this->accession_identifier,
            'collected_at' => isset($this->collected_at) ? Carbon::parse($this->collected_at)->format('d/m/Y h:i:s') : null,
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.specimens.show', ['specimen' => $this->id], false),
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
                'container' => $this->container($this->container),
                'patient' => $this->patient($this->patient),
                'collector' => $this->collector($this->collector),
                'serviceRequest' => $this->serviceRequest($this->serviceRequest),
                'status' => $this->code($this->status),
                'code' => $this->status($this->code)
            ],
        ];
    }

    private function date($date): ?string
    {
        if (!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }

    private function container($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->name,
            '_links' => [
                'self' => [
                    'href' => route('api.containers.show', ['container' => $payload->id], false)
                ]
            ]
        ];
    }

    private function patient($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->humanNames
                ->filter(function ($name) {
                    return $name->use == 'usual' || $name->use == 'official';
                })
                ->map(function ($name) {
                    return [
                        'use' => $name->use,
                        'given' => $name->given,
                        'father_family' => $name->father_family,
                        'mother_family' => $name->mother_family];
                }),
            'birthdate' => Carbon::parse($payload->birthdate)->format('d/m/Y'),
            'administrative_gender' => $payload->administrativeGender->display,
            'identifier' => $payload->identifierPatient
                ->filter(function ($identifier) {
                    return $identifier->identifierUse->code == 'usual' || $identifier->identifierUse->code == 'official';
                })
                ->map(function ($identifier) {
                    return [
                        'use' => $identifier->identifierUse->display,
                        'type' => $identifier->identifierType->display,
                        'value' => $identifier->value,
                    ];
                }),
            '_links' => [
                'self' => [
                    'href' => route('api.patients.show', ['patient' => $payload->id], false)
                ]
            ]
        ];
    }

    private function collector($payload): ?array
    {

        if (!isset($payload)) return null;

        return [
            'name' => $payload->names,
            'father_family' => $payload->lastname,
            'mother_family' => $payload->mother_lastname,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $payload->id], false)
                ]
            ]
        ];
    }

    private function serviceRequest($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'requisition' => $payload->names,
            '_links' => [
                'self' => [
                    'href' => route('api.service-requests.show', ['service_request' => $payload->id], false)
                ]
            ]
        ];
    }

    private function code($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.specimen-codes.show', ['specimen_code' => $payload->id], false)
                ]
            ]
        ];
    }

    private function status($payload): ?array
    {
        if (!isset($payload)) return null;

        return [
            'name' => $payload->display,
            '_links' => [
                'self' => [
                    'href' => route('api.specimen-statuses.show', ['specimen_status' => $payload->id], false)
                ]
            ]
        ];
    }

    private function user($user): ?array
    {
        if (!isset($user)) return null;

        return [
            'name' => $user->names,
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $user->id], false)
                ]
            ]
        ];
    }
}
