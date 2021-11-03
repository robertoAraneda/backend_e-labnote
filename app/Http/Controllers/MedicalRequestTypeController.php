<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicalRequestTypeRequest;
use App\Http\Resources\Collections\MedicalRequestTypeResourceCollection;
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
     * @throws AuthorizationException
     */
    public function index(MedicalRequestTypeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', MedicalRequestType::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = MedicalRequestType::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = MedicalRequestType::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new MedicalRequestTypeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
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

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = MedicalRequestType::create($data);

            return response()->json(new MedicalRequestTypeResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param MedicalRequestType $medicalRequestType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(MedicalRequestType $medicalRequestType): JsonResponse
    {
        $this->authorize('view', $medicalRequestType);

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

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $medicalRequestType->update($data);

            return response()->json(new MedicalRequestTypeResource($medicalRequestType), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param MedicalRequestTypeRequest $request
     * @param MedicalRequestType $medicalRequestType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(MedicalRequestTypeRequest $request, MedicalRequestType $medicalRequestType): JsonResponse
    {
        $this->authorize('delete', $medicalRequestType);

        try {

            $medicalRequestType->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $medicalRequestType->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param MedicalRequestTypeRequest $request
     * @param MedicalRequestType $medicalRequestType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(MedicalRequestTypeRequest $request, MedicalRequestType $medicalRequestType): JsonResponse
    {
        $this->authorize('update', $medicalRequestType);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $medicalRequestType->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new MedicalRequestTypeResource($medicalRequestType), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
