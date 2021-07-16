<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Http\Resources\WorkareaResource;
use App\Models\Availability;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AvailabilityRequest $request
     * @return JsonResponse
     */
    public function index(AvailabilityRequest $request): JsonResponse
    {
        $items = Availability::orderBy('id')->get();

        return response()->json(AvailabilityResource::collection($items), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AvailabilityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AvailabilityRequest $request): JsonResponse
    {
        $this->authorize('create', Availability::class);

        $model = Availability::create($request->validated());

        return response()->json(new AvailabilityResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Availability $availability
     * @return JsonResponse
     */
    public function show(Availability $availability): JsonResponse
    {
        return response()->json(new AvailabilityResource($availability), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AvailabilityRequest $request
     * @param Availability $availability
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $this->authorize('update', $availability);

        $availability->update($request->validated());

        return response()->json(new WorkareaResource($availability), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Availability $availability
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Availability $availability): JsonResponse
    {
        $this->authorize('delete', $availability);

        try {
            $availability->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
