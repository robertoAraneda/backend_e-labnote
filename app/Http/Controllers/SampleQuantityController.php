<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleQuantityRequest;
use App\Http\Resources\collections\SampleQuantityResourceCollection;
use App\Http\Resources\SampleQuantityResource;
use App\Models\SampleQuantity;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SampleQuantityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SampleQuantityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SampleQuantityRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SampleQuantity::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = SampleQuantity::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = SampleQuantity::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SampleQuantityResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param SampleQuantityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(SampleQuantityRequest $request): JsonResponse
    {
        $this->authorize('create', SampleQuantity::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = SampleQuantity::create($data);

            return response()->json(new SampleQuantityResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(SampleQuantity $sampleQuantity): JsonResponse
    {
        $this->authorize('view', $sampleQuantity);

        return response()->json(new SampleQuantityResource($sampleQuantity), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SampleQuantityRequest $request
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SampleQuantityRequest $request, SampleQuantity $sampleQuantity): JsonResponse
    {
        $this->authorize('update', $sampleQuantity);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $sampleQuantity->update($data);

            return response()->json(new SampleQuantityResource($sampleQuantity), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param SampleQuantityRequest $request
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SampleQuantityRequest $request, SampleQuantity $sampleQuantity): JsonResponse
    {
        $this->authorize('delete', $sampleQuantity);

        try {

            $sampleQuantity->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $sampleQuantity->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SampleQuantityRequest $request
     * @param SampleQuantity $sampleQuantity
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(SampleQuantityRequest $request, SampleQuantity $sampleQuantity): JsonResponse
    {
        $this->authorize('update', $sampleQuantity);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $sampleQuantity->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new SampleQuantityResource($sampleQuantity), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
