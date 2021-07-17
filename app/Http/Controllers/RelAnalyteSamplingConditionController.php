<?php

namespace App\Http\Controllers;

use App\Http\Resources\collections\SamplingConditionResource;
use App\Models\Analyte;
use App\Models\SamplingCondition;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelAnalyteSamplingConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Analyte $analyte
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request, Analyte $analyte): JsonResponse
    {
        $this->authorize('view', $analyte);

        if($request->input('cross')){

            $allSamplingConditions = SamplingCondition::active()->orderBy('id')->get();

            $analyteSamplingConditions = $analyte->samplingConditions()->orderBy('id')->get()->pluck('id');

            $samplingConditions = $allSamplingConditions->map(function ($samplingIndication) use ($analyteSamplingConditions){

                $samplingIndication->checkbox = in_array($samplingIndication->id, $analyteSamplingConditions->all());
                return $samplingIndication;
            });


        }else{
            $samplingConditions = $analyte->samplingConditions()->active()->orderBy('id')->get();
        }

        return response()->json(SamplingConditionResource::collection($samplingConditions), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request, Analyte $analyte): JsonResponse
    {
        $analyte->samplingConditions()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $analyte->samplingConditions()->orderBy('id')->get();

        return response()->json(SamplingConditionResource::collection($collection), 200);
    }
}
