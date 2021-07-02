<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkareaRequest;
use App\Http\Resources\WorkareaResource;
use App\Models\Workarea;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WorkareaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param WorkareaRequest $request
     * @return JsonResponse
     */
    public function index(WorkareaRequest $request): JsonResponse
    {
        $items = Workarea::orderBy('id')->paginate($request->getPaginate());

        return response()->json(
            WorkareaResource::collection($items)
                ->response()
                ->getData(true),
            Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param WorkareaRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(WorkareaRequest $request): JsonResponse
    {
        $this->authorize('create', Workarea::class);

        $model = Workarea::create($request->validated());

        return response()->json(new WorkareaResource($model->fresh()), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Workarea $workarea
     * @return JsonResponse
     */
    public function show(Workarea $workarea): JsonResponse
    {
        return response()->json(new WorkareaResource($workarea), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WorkareaRequest $request
     * @param Workarea $workarea
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(WorkareaRequest $request, Workarea $workarea): JsonResponse
    {
        $this->authorize('update', $workarea);

        $workarea->update($request->validated());

        return response()->json(new WorkareaResource($workarea), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Workarea $workarea
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(Workarea $workarea): JsonResponse
    {

        $this->authorize('delete', $workarea);

        try {
            $workarea->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        }catch (\Exception $exception){

            return response()->json(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
