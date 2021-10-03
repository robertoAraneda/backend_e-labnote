<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Http\Resources\CityResource;
use App\Http\Resources\collections\CityResourceCollection;
use App\Models\City;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CityController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param CityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(CityRequest $request): JsonResponse
    {
        $this->authorize('viewAny', City::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = City::select(
                'code',
                'name',
                'state_code',
                'active',
            )
                ->orderBy('code')
                ->paginate($request->getPaginate());
        }else{
            $items = City::select(
                'code',
                'name',
                'state_code',
                'active',
            )
                ->orderBy('code')
                ->get();
        }
        $collection = new CityResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(CityRequest $request): JsonResponse
    {
        $this->authorize('create', City::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = City::create($data);

            return response()->json(new CityResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param City $city
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(City $city): JsonResponse
    {
        $this->authorize('view', $city);

        return response()->json(new CityResource($city), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityRequest $request
     * @param City $city
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(CityRequest $request, City $city): JsonResponse
    {
        $this->authorize('update', $city);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $city->update($data);

            return response()->json(new CityResource($city) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CityRequest $request
     * @param City $city
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(CityRequest $request, City $city): JsonResponse
    {
        $this->authorize('delete', $city);

        try {

            $city->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $city->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param CityRequest $request
     * @param City $city
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(CityRequest $request, City $city): JsonResponse
    {
        $this->authorize('update', $city);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $city->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new CityResource($city), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
