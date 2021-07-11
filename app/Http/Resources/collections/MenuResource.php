<?php

namespace App\Http\Resources\collections;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'active' => (bool) $this->active,
            '_link' => [
                'self' =>[
                    'href' => route('api.menus.show', ['menu' => $this->id], false),
                ] ,
            ],
        ];
    }
}
