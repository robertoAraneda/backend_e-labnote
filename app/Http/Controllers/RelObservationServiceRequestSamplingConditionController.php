<?php

namespace App\Http\Controllers;

use App\Http\Resources\collections\SamplingConditionResource;
use App\Models\Analyte;
use App\Models\ServiceRequestObservation;
use App\Models\SamplingCondition;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelObservationServiceRequestSamplingConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param ServiceRequestObservation $observationServiceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request, ServiceRequestObservation $observationServiceRequest): JsonResponse
    {
        $this->authorize('view', $observationServiceRequest);

        if($request->input('cross')){

            $allSamplingConditions = SamplingCondition::active()->orderBy('id')->get();

            $observationSamplingConditions = $observationServiceRequest->samplingConditions()->orderBy('id')->get()->pluck('id');

            $samplingConditions = $allSamplingConditions->map(function ($samplingCondition) use ($observationSamplingConditions){

                $samplingCondition->checkbox = in_array($samplingCondition->id, $observationSamplingConditions->all());
                return $samplingCondition;
            });


        }else{
            $samplingConditions = $observationServiceRequest->samplingConditions()->active()->orderBy('id')->get();
        }

        return response()->json(SamplingConditionResource::collection($samplingConditions), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param ServiceRequestObservation $observationServiceRequest
     * @return JsonResponse
     */
    public function store(Request $request, ServiceRequestObservation $observationServiceRequest): JsonResponse
    {
        $observationServiceRequest->samplingConditions()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $observationServiceRequest->samplingConditions()->orderBy('id')->get();

        return response()->json(SamplingConditionResource::collection($collection), 200);
    }
}
