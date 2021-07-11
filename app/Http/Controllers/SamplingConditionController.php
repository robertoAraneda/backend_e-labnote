<?php

namespace App\Http\Controllers;

use App\Http\Requests\SamplingConditionRequest;
use App\Http\Resources\SamplingConditionResource;
use App\Models\SamplingCondition;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class SamplingConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SamplingConditionRequest $request
     * @return JsonResponse
     */
    public function index(SamplingConditionRequest $request): JsonResponse
    {
        $items = SamplingCondition::orderBy('id')->get();

        return response()->json(SamplingConditionResource::collection($items), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SamplingConditionRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(SamplingConditionRequest $request): JsonResponse
    {
        $this->authorize('create', SamplingCondition::class);

        $model = SamplingCondition::create($request->validated());

        return response()->json(new SamplingConditionResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param SamplingCondition $samplingCondition
     * @return JsonResponse
     */
    public function show(SamplingCondition $samplingCondition): JsonResponse
    {
        return response()->json(new SamplingConditionResource($samplingCondition), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SamplingConditionRequest $request, SamplingCondition $samplingCondition): JsonResponse
    {
        $this->authorize('update', $samplingCondition);

        $samplingCondition->update($request->validated());

        return response()->json(new SamplingConditionResource($samplingCondition), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SamplingCondition $samplingCondition
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SamplingCondition $samplingCondition): JsonResponse
    {
        $this->authorize('delete', $samplingCondition);

        try {
            $samplingCondition->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
