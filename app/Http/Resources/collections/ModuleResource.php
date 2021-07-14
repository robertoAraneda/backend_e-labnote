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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'url' => $this->url,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'active' => (bool) $this->active,
            '_link' => [
                'self' =>[
                    'href' => route('api.modules.show', ['module' => $this->id], false),
                ] ,
                'menus' =>[
                    'href' => route('api.module.menus', ['module' => $this->id], false),
                ] ,
            ],
        ];
    }
}
