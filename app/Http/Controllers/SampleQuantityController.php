<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleQuantityRequest;
use App\Http\Resources\SampleQuantityResource;
use App\Models\SampleQuantity;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SampleQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SampleQuantityRequest $request): JsonResponse
    {
        $items = SampleQuantity::orderBy('id')->get();

        return response()->json(SampleQuantityResource::collection($items), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SampleQuantityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(SampleQuantityRequest $request): JsonResponse
    {
        $this->authorize('create', SampleQuantity::class);

        $model = SampleQuantity::create($request->validated());

        return response()->json(new SampleQuantityResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     */
    public function show(SampleQuantity $sampleQuantity): JsonResponse
    {
        return response()->json(new SampleQuantityResource($sampleQuantity), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SampleQuantityRequest $request
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SampleQuantityRequest $request, SampleQuantity $sampleQuantity): JsonResponse
    {
        $this->authorize('update', $sampleQuantity);

        $sampleQuantity->update($request->validated());

        return response()->json(new SampleQuantityResource($sampleQuantity), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SampleQuantity $sampleQuantity): JsonResponse
    {
        $this->authorize('delete', $sampleQuantity);

        try {
            $sampleQuantity->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
