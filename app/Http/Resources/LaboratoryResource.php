<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryResource extends JsonResource
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
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'redirect' => $this->redirect,
            'technical_director' => $this->technical_director,
            'active' => (bool) $this->active,
            'createdUserIp' => $this->created_user_ip,
            'updatedUserIp' => $this->updated_user_ip,
            'createdAt' => $this->date($this->created_at),
            'updatedAt' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.users.show', ['user' => $this->id], false),
                ]
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
            ],
        ];
    }

    private function date($date){
        if(!isset($date)) return null;

        return $date->format('d/m/Y h:i:s');
    }

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
