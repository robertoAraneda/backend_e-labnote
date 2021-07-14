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
                'checkbox' =>(bool) $this->checkbox,
                'createdUserIp' => $this->created_user_ip,
                'updatedUserIp' => $this->updated_user_ip,
                'createdAt' => $this->date($this->created_at),
                'updatedAt' => $this->date($this->updated_at),
                '_links' => [
                    'self' => [
                        'href' => route('api.modules.show', ['module' => $this->id], false),
                    ]
                ],
                '_embedded' => [
                    'createdUser' => $this->user($this->createdUser),
                    'updatedUser' => $this->user($this->updatedUser),
                ],
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
            'createdUserIp' => $this->created_user_ip,
            'updatedUserIp' => $this->updated_user_ip,
            'createdAt' => $this->date($this->created_at),
            'updatedAt' => $this->date($this->updated_at),
            '_links' => [
                'self' => [
                    'href' => route('api.modules.show', ['module' => $this->id], false),
                ],
                'menus' => [
                    'href' => route('api.module.menus', ['module' => $this->id], false),
                ],
                'permissions' => [
                    'href' => route('api.modules.permissions.index', ['module' => $this->id], false),
                ],
            ],
            '_embedded' => [
                'createdUser' => $this->user($this->createdUser),
                'updatedUser' => $this->user($this->updatedUser),
                'menus' => $this->menus($this->menus),
                'permissions' => $this->permissions($this->permissions),
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

    private function menus($menus){
        if(!isset($menus)) return null;

        return $menus->map(function($menu) {
          return  [
                'name' => $menu->name,
                'triggerPermission' => $menu->permission->name,
                '_links' => [
                    'self' => route('api.menus.show', ['menu' => $menu->id], false),
                ]
            ];
        });
    }

    private function permissions($permissions){
        if(!isset($permissions)) return null;

        return $permissions->map(function($permission) {
            return  [
                'name' => $permission->name,
                '_links' => [
                    'self' => route('api.menus.show', ['menu' => $permission->id], false),
                ]
            ];
        });
    }
}
