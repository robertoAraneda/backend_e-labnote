<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessTimeRequest;
use App\Http\Resources\ProcessTimeResource;
use App\Models\ProcessTime;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcessTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProcessTimeRequest $request
     * @return JsonResponse
     */
    public function index(ProcessTimeRequest $request): JsonResponse
    {
        $items = ProcessTime::orderBy('id')->get();

        return response()->json(ProcessTimeResource::collection($items), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProcessTimeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ProcessTimeRequest $request): JsonResponse
    {
        $this->authorize('create', ProcessTime::class);

        $model = ProcessTime::create($request->validated());

        return response()->json(new ProcessTimeResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ProcessTime $processTime)
    {
        return response()->json(new ProcessTimeResource($processTime), Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ProcessTimeRequest $request
     * @param ProcessTime $processTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ProcessTimeRequest $request, ProcessTime $processTime): JsonResponse
    {
        $this->authorize('update', $processTime);

        $processTime->update($request->validated());

        return response()->json(new ProcessTimeResource($processTime), Response::HTTP_OK);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProcessTime $processTime): JsonResponse
    {
        $this->authorize('delete', $processTime);

        try {
            $processTime->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
