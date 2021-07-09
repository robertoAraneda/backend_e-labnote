<?php

namespace App\Http\Resources;

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
                'icon' => $this->icon,
                'url' => $this->url,
                'slug' => $this->slug,
                'active' => (bool) $this->active,
                'checkbox' =>(bool) $this->checkbox
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'url' => $this->url,
            'slug' => $this->slug,
            'menus' => $this->menus,
            'active' => (bool) $this->active,
        ];

    }
}
