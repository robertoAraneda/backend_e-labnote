<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        if(isset($this->checkbox)){
            return [
                'id' => $this->id,
                'name' => $this->name,
                'model' => $this->model,
                'action' => $this->action,
                'guard_name' => $this->guard_name,
                'active' => $this->active,
                'description' => $this->description,
                'checkbox' =>(bool) $this->checkbox,
                '_links' => [
                    'self' => [
                        'href' => route('api.permissions.show', ['permission' => $this->id], false),
                    ]
                ],
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'action' => $this->action,
            'active' => $this->active,
            'guard_name' => $this->guard_name,
            'description' => $this->description,
            '_links' => [
                'self' => [
                    'href' => route('api.permissions.show', ['permission' => $this->id], false),
                ]
            ],
        ];
    }
}
