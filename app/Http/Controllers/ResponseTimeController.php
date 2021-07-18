<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResponseTimeRequest;
use App\Http\Resources\collections\ResponseTimeResourceCollection;
use App\Http\Resources\ResponseTimeResource;
use App\Models\ResponseTime;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ResponseTimeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ResponseTimeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ResponseTime::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = ResponseTime::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = ResponseTime::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ResponseTimeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ResponseTimeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ResponseTimeRequest $request): JsonResponse
    {
        $this->authorize('create', ResponseTime::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = ResponseTime::create($data);

            return response()->json(new ResponseTimeResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ResponseTime $responseTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ResponseTime $responseTime): JsonResponse
    {
        $this->authorize('view', $responseTime);

        return response()->json(new ResponseTimeResource($responseTime), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ResponseTimeRequest $request
     * @param ResponseTime $responseTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ResponseTimeRequest $request, ResponseTime $responseTime): JsonResponse
    {
        $this->authorize('update', $responseTime);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $responseTime->update($data);

            return response()->json(new ResponseTimeResource($responseTime), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ResponseTimeRequest $request
     * @param ResponseTime $responseTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ResponseTimeRequest $request, ResponseTime $responseTime): JsonResponse
    {
        $this->authorize('delete', $responseTime);

        try {

            $responseTime->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $responseTime->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ResponseTimeRequest $request
     * @param ResponseTime $responseTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ResponseTimeRequest $request, ResponseTime $responseTime): JsonResponse
    {
        $this->authorize('update', $responseTime);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $responseTime->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ResponseTimeResource($responseTime), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
