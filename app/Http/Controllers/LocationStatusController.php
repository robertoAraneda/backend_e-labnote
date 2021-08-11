<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationStatusRequest;
use App\Http\Resources\collections\LocationStatusResourceCollection;
use App\Http\Resources\LocationStatusResource;
use App\Models\LocationStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LocationStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LocationStatusRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(LocationStatusRequest $request): JsonResponse
    {
        $this->authorize('viewAny', LocationStatus::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = LocationStatus::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = LocationStatus::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new LocationStatusResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LocationStatusRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LocationStatusRequest $request): JsonResponse
    {
        $this->authorize('create', LocationStatus::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = LocationStatus::create($data);

            return response()->json(new LocationStatusResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param LocationStatus $locationStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(LocationStatus $locationStatus): JsonResponse
    {
        $this->authorize('view', $locationStatus);

        return response()->json(new LocationStatusResource($locationStatus), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LocationStatusRequest $request
     * @param LocationStatus $locationStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(LocationStatusRequest $request, LocationStatus $locationStatus): JsonResponse
    {
        $this->authorize('update', $locationStatus);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $locationStatus->update($data);

            return response()->json(new LocationStatusResource($locationStatus) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LocationStatusRequest $request
     * @param LocationStatus $locationStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(LocationStatusRequest $request, LocationStatus $locationStatus): JsonResponse
    {
        $this->authorize('delete', $locationStatus);

        try {

            $locationStatus->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $locationStatus->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param LocationStatusRequest $request
     * @param LocationStatus $locationStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(LocationStatusRequest $request, LocationStatus $locationStatus): JsonResponse
    {
        $this->authorize('update', $locationStatus);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $locationStatus->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new LocationStatusResource($locationStatus), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
