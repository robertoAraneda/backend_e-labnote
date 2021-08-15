<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Resources\collections\LocationResourceCollection;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param LocationRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(LocationRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Location::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Location::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Location::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new LocationResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LocationRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(LocationRequest $request): JsonResponse
    {
        $this->authorize('create', Location::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Location::create($data);

            return response()->json(new LocationResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Location $location
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Location $location): JsonResponse
    {
        $this->authorize('view', $location);

        return response()->json(new LocationResource($location), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LocationRequest $request
     * @param Location $location
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(LocationRequest $request, Location $location): JsonResponse
    {
        $this->authorize('update', $location);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $location->update($data);

            return response()->json(new LocationResource($location) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param LocationRequest $request
     * @param Location $location
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(LocationRequest $request, Location $location): JsonResponse
    {
        $this->authorize('delete', $location);

        try {

            $location->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $location->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
