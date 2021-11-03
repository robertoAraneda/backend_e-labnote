<?php

namespace App\Http\Controllers;

use App\Http\Resources\Collections\ModuleResource;
use App\Models\Laboratory;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelLaboratoryModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Laboratory $laboratory
     * @return JsonResponse
     */
    public function index(Request $request, Laboratory $laboratory):JsonResponse
    {

        if($request->input('cross')){

            $allModules = Module::active()->orderBy('id')->get();
            $modulesLab = $laboratory->modules()->orderBy('id')->get()->pluck('id');

            $modules = $allModules->map(function ($module) use ($modulesLab){

                $module->checkbox = in_array($module->id, $modulesLab->all());
                return $module;
            });


        }else{
            $modules = $laboratory->modules()->active()->orderBy('id')->get();
        }

        return response()->json(ModuleResource::collection($modules), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Laboratory $laboratory
     * @return JsonResponse
     */
    public function store(Request $request, Laboratory $laboratory):JsonResponse
    {
        $laboratory->modules()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $laboratory->modules()->orderBy('id')->get();

        return response()->json(ModuleResource::collection($collection), 200);
    }
}
