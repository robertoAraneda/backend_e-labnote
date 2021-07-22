<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistrictRequest;
use App\Http\Resources\collections\DistrictResourceCollection;
use App\Http\Resources\DistrictResource;
use App\Models\District;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param DistrictRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(DistrictRequest $request): JsonResponse
    {
        $this->authorize('viewAny', District::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = District::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = District::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new DistrictResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DistrictRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(DistrictRequest $request): JsonResponse
    {
        $this->authorize('create', District::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = District::create($data);

            return response()->json(new DistrictResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param District $district
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(District $district): JsonResponse
    {
        $this->authorize('view', $district);

        return response()->json(new DistrictResource($district), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param DistrictRequest $request
     * @param District $district
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(DistrictRequest $request, District $district): JsonResponse
    {
        $this->authorize('update', $district);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $district->update($data);

            return response()->json(new DistrictResource($district) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DistrictRequest $request
     * @param District $district
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(DistrictRequest $request, District $district): JsonResponse
    {
        $this->authorize('delete', $district);

        try {

            $district->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $district->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param DistrictRequest $request
     * @param District $district
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(DistrictRequest $request, District $district): JsonResponse
    {
        $this->authorize('update', $district);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $district->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new DistrictResource($district), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
