<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
                'url' => $this->url,
                'slug' => $this->slug,
                'icon' => $this->icon,
                'checkbox' => $this->checkbox,
                'active' => (bool) $this->active,
                '_links' => [
                    'self' =>[
                        'href' => route('api.modules.show', ['module' => $this->id], false),
                    ] ,
                ],
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.modules.show', ['module' => $this->id], false),
                ] ,
            ],
        ];

    }
}
