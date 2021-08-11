<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationTypeRequest;
use App\Http\Resources\collections\LocationTypeResourceCollection;
use App\Http\Resources\LocationTypeResource;
use App\Models\LocationType;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LocationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LocationTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(LocationTypeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', LocationType::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = LocationType::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = LocationType::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new LocationTypeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LocationTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LocationTypeRequest $request): JsonResponse
    {
        $this->authorize('create', LocationType::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = LocationType::create($data);

            return response()->json(new LocationTypeResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param LocationType $locationType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(LocationType $locationType): JsonResponse
    {
        $this->authorize('view', $locationType);

        return response()->json(new LocationTypeResource($locationType), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LocationTypeRequest $request
     * @param LocationType $locationType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(LocationTypeRequest $request, LocationType $locationType): JsonResponse
    {
        $this->authorize('update', $locationType);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $locationType->update($data);

            return response()->json(new LocationTypeResource($locationType) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LocationTypeRequest $request
     * @param LocationType $locationType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(LocationTypeRequest $request, LocationType $locationType): JsonResponse
    {
        $this->authorize('delete', $locationType);

        try {

            $locationType->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $locationType->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param LocationTypeRequest $request
     * @param LocationType $locationType
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(LocationTypeRequest $request, LocationType $locationType): JsonResponse
    {
        $this->authorize('update', $locationType);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $locationType->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new LocationTypeResource($locationType), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
