<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResponseTimeRequest;
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
     */
    public function index(ResponseTimeRequest $request): JsonResponse
    {
        $items = ResponseTime::orderBy('id')->get();

        return response()->json(ResponseTimeResource::collection($items), Response::HTTP_OK);
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

        $model = ResponseTime::create($request->validated());

        return response()->json(new ResponseTimeResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param ResponseTime $responseTime
     * @return JsonResponse
     */
    public function show(ResponseTime $responseTime): JsonResponse
    {
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

        $responseTime->update($request->validated());

        return response()->json(new ResponseTimeResource($responseTime), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ResponseTime $responseTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ResponseTime $responseTime): JsonResponse
    {
        $this->authorize('delete', $responseTime);

        try {
            $responseTime->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
