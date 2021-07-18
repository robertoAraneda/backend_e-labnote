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
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'module' => $this->module,
            'module_id' => $this->module->id,
            'permission_id' => $this->permission->id,
            'permission' => $this->permission,
            'order' => (int) $this->order,
            'active' => (bool) $this->active,
            '_links' => [
                'self' =>[
                    'href' => route('api.menus.show', ['menu' => $this->id], false),
                ] ,
            ],
        ];
    }
}
