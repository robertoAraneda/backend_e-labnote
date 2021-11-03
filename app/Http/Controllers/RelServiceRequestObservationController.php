<?php

namespace App\Http\Controllers;

use App\Http\Resources\Collections\ServiceRequestResource;
use App\Models\ServiceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelServiceRequestObservationController extends Controller
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
     * @param Request $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function store(Request $request, ServiceRequest $serviceRequest):JsonResponse
    {
        $serviceRequest->observationServiceRequests()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $serviceRequest->observationServiceRequests()->orderBy('id')->get();

        return response()->json(ServiceRequestResource::collection($collection), 200);
    }
}
