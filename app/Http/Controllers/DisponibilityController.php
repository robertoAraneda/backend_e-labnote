<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisponibilityRequest;
use App\Http\Resources\DisponibilityResource;
use App\Http\Resources\WorkareaResource;
use App\Models\Disponibility;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DisponibilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param DisponibilityRequest $request
     * @return JsonResponse
     */
    public function index(DisponibilityRequest $request): JsonResponse
    {
        $items = Disponibility::orderBy('id')->get();

        return response()->json(DisponibilityResource::collection($items), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DisponibilityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(DisponibilityRequest $request): JsonResponse
    {
        $this->authorize('create', Disponibility::class);

        $model = Disponibility::create($request->validated());

        return response()->json(new DisponibilityResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Disponibility $disponibility
     * @return JsonResponse
     */
    public function show(Disponibility $disponibility): JsonResponse
    {
        return response()->json(new DisponibilityResource($disponibility), Response::HTTP_OK);    }

    /**
     * Update the specified resource in storage.
     *
     * @param DisponibilityRequest $request
     * @param Disponibility $disponibility
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(DisponibilityRequest $request, Disponibility $disponibility): JsonResponse
    {
        $this->authorize('update', $disponibility);

        $disponibility->update($request->validated());

        return response()->json(new WorkareaResource($disponibility), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Disponibility $disponibility
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Disponibility $disponibility): JsonResponse
    {
        $this->authorize('delete', $disponibility);

        try {
            $disponibility->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
