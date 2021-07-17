<?php

namespace App\Http\Controllers;

use App\Http\Resources\collections\SamplingIndicationResource;
use App\Models\Specimen;
use App\Models\SamplingIndication;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RelSpecimenSamplingIndicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Specimen $specimen
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request, Specimen $specimen): JsonResponse
    {
        $this->authorize('view', $specimen);

        if ($request->input('cross')) {

            $allSamplingIndications = SamplingIndication::active()->orderBy('id')->get();

            $sampleTypeSamplingConditions = $specimen->samplingIndications()->orderBy('id')->get()->pluck('id');

            $samplingIndications = $allSamplingIndications->map(function ($samplingIndication) use ($sampleTypeSamplingConditions) {

                $samplingIndication->checkbox = in_array($samplingIndication->id, $sampleTypeSamplingConditions->all());
                return $samplingIndication;
            });


        } else {
            $samplingIndications = $specimen->samplingIndications()->active()->orderBy('id')->get();
        }

        return response()->json(SamplingIndicationResource::collection($samplingIndications), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Specimen $specimen
     * @return JsonResponse
     */
    public function store(Request $request, Specimen $specimen): JsonResponse
    {
        $specimen->samplingIndications()->syncWithPivotValues($request->all(), ['user_id' => auth()->id()]);

        $collection = $specimen->samplingIndications()->orderBy('id')->get();

        return response()->json(SamplingIndicationResource::collection($collection), 200);
    }

}
