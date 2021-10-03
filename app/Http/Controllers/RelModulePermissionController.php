<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelModulePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Module $module
     * @return JsonResponse
     */
    public function index(Request $request, Module $module): JsonResponse
    {
        if($request->input('cross')){

            $module_permissions = $module->permissions()->orderBy('id')->get();

            $role = Role::find($request->input('role_id'));

            $role_permission = $role->permissions()->pluck('id');

            $permissions = $module_permissions->map(function ($permission) use ($role_permission){

                $permission->checkbox = in_array($permission->id, $role_permission->all());
                return $permission;
            });

        }else{
            $permissions = $module->permissions()->orderBy('id')->get();
        }

        return response()->json(PermissionResource::collection($permissions), 200);
    }

    public function allModulesWithPermissions(Request $request){

        $modules = $request->input('modules');

        if(is_array($modules)){
            $modulesResponse = [];

            foreach ($modules as $module){
                $modulesResponse[$module] = Module::where('slug', $module)->first()->menus;
            }

          return $modulesResponse;
        }
        return [];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, Module $module): JsonResponse
    {
        $module->permissions()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $module->permissions()->orderBy('id')->get();

        return response()->json(PermissionResource::collection($collection), 200);
    }
}
