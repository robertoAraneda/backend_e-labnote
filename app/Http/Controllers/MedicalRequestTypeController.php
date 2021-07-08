<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalRequestTypeRequest;
use App\Http\Resources\MedicalRequestTypeResource;
use App\Models\MedicalRequestType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicalRequestTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param MedicalRequestTypeRequest $request
     * @return JsonResponse
     */
    public function index(MedicalRequestTypeRequest $request): JsonResponse
    {
        $items = MedicalRequestType::orderBy('id')->get();

        return response()->json(MedicalRequestTypeResource::collection($items), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MedicalRequestTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(MedicalRequestTypeRequest $request): JsonResponse
    {
        $this->authorize('create', MedicalRequestType::class);

        $model = MedicalRequestType::create($request->validated());

        return response()->json(new MedicalRequestTypeResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param MedicalRequestType $medicalRequestType
     * @return JsonResponse
     */
    public function show(MedicalRequestType $medicalRequestType): JsonResponse
    {
        return response()->json(new MedicalRequestTypeResource($medicalRequestType), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MedicalRequestTypeRequest $request
     * @param MedicalRequestType $medicalRequestType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(MedicalRequestTypeRequest $request, MedicalRequestType $medicalRequestType): JsonResponse
    {
        $this->authorize('update', $medicalRequestType);

        $medicalRequestType->update($request->validated());

        return response()->json(new MedicalRequestTypeResource($medicalRequestType), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MedicalRequestType $medicalRequestType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(MedicalRequestType $medicalRequestType): JsonResponse
    {
        $this->authorize('delete', $medicalRequestType);

        try {
            $medicalRequestType->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
