<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkareaRequest;
use App\Http\Resources\Collections\WorkareaResourceCollection;
use App\Http\Resources\WorkareaResource;
use App\Models\Module;
use App\Models\Workarea;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class WorkareaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param WorkareaRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(WorkareaRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Workarea::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = Workarea::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = Workarea::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new WorkareaResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);

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

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Workarea::create($data);

            return response()->json(new WorkareaResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Workarea $workarea
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Workarea $workarea): JsonResponse
    {
        $this->authorize('view', $workarea);

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

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $workarea->update($data);

            return response()->json(new WorkareaResource($workarea) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param WorkareaRequest $request
     * @param Workarea $workarea
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(WorkareaRequest $request, Workarea $workarea): JsonResponse
    {

        $this->authorize('delete', $workarea);

        try {

            $workarea->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $workarea->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param WorkareaRequest $request
     * @param Workarea $workarea
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(WorkareaRequest $request, Workarea $workarea): JsonResponse
    {
        $this->authorize('update', $workarea);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $workarea->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new WorkareaResource($workarea), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
