<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessTimeRequest;
use App\Http\Resources\collections\ProcessTimeResourceCollection;
use App\Http\Resources\ProcessTimeResource;
use App\Models\ProcessTime;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class ProcessTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProcessTimeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ProcessTimeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ProcessTime::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = ProcessTime::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = ProcessTime::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ProcessTimeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
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

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = ProcessTime::create($data);

            return response()->json(new ProcessTimeResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param ProcessTime $processTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ProcessTime $processTime): JsonResponse
    {
        $this->authorize('view', $processTime);

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

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $processTime->update($data);

            return response()->json(new ProcessTimeResource($processTime), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param ProcessTimeRequest $request
     * @param ProcessTime $processTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ProcessTimeRequest $request, ProcessTime $processTime): JsonResponse
    {
        $this->authorize('delete', $processTime);

        try {
            $processTime->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $processTime->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param ProcessTimeRequest $request
     * @param ProcessTime $processTime
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ProcessTimeRequest $request, ProcessTime $processTime): JsonResponse
    {
        $this->authorize('update', $processTime);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $processTime->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ProcessTimeResource($processTime), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
