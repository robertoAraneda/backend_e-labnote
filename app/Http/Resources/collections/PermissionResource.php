<?php

namespace App\Http\Resources\collections;

use App\Models\Menu;
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'menu' => $this->menu($this->id),
            'module' => $this->module($this->id),
            'guard_name' => $this->guard_name,
            'action' => $this->action,
            'active' => $this->active,
            'description' => $this->description,
            '_links' => [
                'self' =>[
                    'href' => route('api.permissions.show', ['permission' => $this->id], false),
                ] ,
            ],
        ];
    }

    public function menu($permission_id){

       return Menu::where('permission_id', $permission_id)->first();

    }

    public function module($permission_id){

        $menu = Menu::where('permission_id', $permission_id)->first();
        if(isset($menu)){
            return $menu->module;
        }
        return $menu;
    }
}
