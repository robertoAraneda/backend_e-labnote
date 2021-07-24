<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' =>$this->name($this->humanNames),
            'telecom' => $this->telecom($this->contactPointPatient),
            'address' => $this->address($this->addressPatient),
            'contact' => $this->contact($this->contactPatient),
            'gender' => $this->administrativeGender->display,
            'birthdate' => $this->birthdate,
            'active' => (bool) $this->active,
            'created_user_ip' => $this->created_user_ip,
            'updated_user_ip' => $this->updated_user_ip,
            'created_at' => $this->date($this->created_at),
            'updated_at' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.patients.show', ['patient' => $this->id], false),
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
            ],
        ];
    }

    /**
     * @param $date
     * @return string|null
     */

    private function date($date): ?string
    {
        if(!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }

    private function telecom($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'system' => $item->system,
                'value' => $item->value,
                'use' => $item->use
            ];
        });

    }

    private function address($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'use' => $item->use,
                'text' => $item->text,
                'city'  => $item->city->name,
                'state' => $item->state->name,
            ];
        });

    }

    private function contact($array){
        if(count($array) === 0) return $array;

        return $array->map(function ($item){
            return [
                'given' => $item->given,
                'family' => $item->family,
                'relationship'  => $item->relationship,
                'email' => $item->email,
                'phone' => $item->phone,
            ];
        });

    }

    private function name($array){
        if(count($array) === 0) return $array;

       return $array->map(function ($item){
           return [
               'use' => $item->use,
               'given' => $item->given,
               'father_family' => $item->father_family,
               'mother_family' => $item->mother_family,
               '_links' => [
                   'self' => [
                       'href' => route('api.users.show', ['user' => $item->id], false)
                   ]
               ]
           ];
       });

    }


    /**
     * @param $user
     * @return array|null
     */

    private function user($user): ?array
    {
        if(!isset($user)) return null;

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
