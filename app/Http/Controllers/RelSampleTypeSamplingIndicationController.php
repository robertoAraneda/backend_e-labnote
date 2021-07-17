<?php

namespace App\Http\Controllers;

use App\Http\Resources\collections\SamplingIndicationResource;
use App\Models\SampleType;
use App\Models\SamplingIndication;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelSampleTypeSamplingIndicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param SampleType $sampleType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request, SampleType $sampleType): JsonResponse
    {
        $this->authorize('view', $sampleType);

        if($request->input('cross')){

            $allSamplingIndications = SamplingIndication::active()->orderBy('id')->get();

            $sampleTypeSamplingConditions = $sampleType->samplingIndications()->orderBy('id')->get()->pluck('id');

            $samplingIndications = $allSamplingIndications->map(function ($samplingIndication) use ($sampleTypeSamplingConditions){

                $samplingIndication->checkbox = in_array($samplingIndication->id, $sampleTypeSamplingConditions->all());
                return $samplingIndication;
            });


        }else{
            $samplingIndications = $sampleType->samplingIndications()->active()->orderBy('id')->get();
        }

        return response()->json(SamplingIndicationResource::collection($samplingIndications), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param SampleType $sampleType
     * @return JsonResponse
     */
    public function store(Request $request, SampleType $sampleType): JsonResponse
    {
        $sampleType->samplingIndications()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $sampleType->samplingIndications()->orderBy('id')->get();

        return response()->json(SamplingIndicationResource::collection($collection), 200);
    }

}
