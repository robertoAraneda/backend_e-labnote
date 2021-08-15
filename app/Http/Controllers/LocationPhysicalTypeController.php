<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationPhysicalTypeRequest;
use App\Http\Resources\collections\LocationPhysicalTypeResourceCollection;
use App\Http\Resources\LocationPhysicalTypeResource;
use App\Models\LocationPhysicalType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationPhysicalTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LocationPhysicalTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(LocationPhysicalTypeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', LocationPhysicalType::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = LocationPhysicalType::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = LocationPhysicalType::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new LocationPhysicalTypeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LocationPhysicalTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LocationPhysicalTypeRequest $request): JsonResponse
    {
        $this->authorize('create', LocationPhysicalType::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = LocationPhysicalType::create($data);

            return response()->json(new LocationPhysicalTypeResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param LocationPhysicalType $locationPhysicalType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(LocationPhysicalType $locationPhysicalType): JsonResponse
    {
        $this->authorize('view', $locationPhysicalType);

        return response()->json(new LocationPhysicalTypeResource($locationPhysicalType), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LocationPhysicalTypeRequest $request
     * @param LocationPhysicalType $locationPhysicalType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(LocationPhysicalTypeRequest $request, LocationPhysicalType $locationPhysicalType): JsonResponse
    {
        $this->authorize('update', $locationPhysicalType);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $locationPhysicalType->update($data);

            return response()->json(new LocationPhysicalTypeResource($locationPhysicalType) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LocationPhysicalTypeRequest $request
     * @param LocationPhysicalType $locationPhysicalType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(LocationPhysicalTypeRequest $request, LocationPhysicalType $locationPhysicalType): JsonResponse
    {
        $this->authorize('delete', $locationPhysicalType);

        try {

            $locationPhysicalType->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $locationPhysicalType->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param LocationPhysicalTypeRequest $request
     * @param LocationPhysicalType $locationPhysicalType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(LocationPhysicalTypeRequest $request, LocationPhysicalType $locationPhysicalType): JsonResponse
    {
        $this->authorize('update', $locationPhysicalType);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $locationPhysicalType->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new LocationPhysicalTypeResource($locationPhysicalType), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
