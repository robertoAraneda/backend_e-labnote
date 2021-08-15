<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestPriorityRequest;
use App\Http\Resources\collections\ServiceRequestPriorityResourceCollection;
use App\Http\Resources\ServiceRequestPriorityResource;
use App\Models\ServiceRequestPriority;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestPriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestPriorityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestPriorityRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequestPriority::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = ServiceRequestPriority::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = ServiceRequestPriority::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestPriorityResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestPriorityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestPriorityRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceRequestPriority::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = ServiceRequestPriority::create($data);

            return response()->json(new ServiceRequestPriorityResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequestPriority $serviceRequestPriority
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequestPriority $serviceRequestPriority): JsonResponse
    {
        $this->authorize('view', $serviceRequestPriority);

        return response()->json(new ServiceRequestPriorityResource($serviceRequestPriority), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestPriorityRequest $request
     * @param ServiceRequestPriority $serviceRequestPriority
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestPriorityRequest $request, ServiceRequestPriority $serviceRequestPriority): JsonResponse
    {
        $this->authorize('update', $serviceRequestPriority);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $serviceRequestPriority->update($data);

            return response()->json(new ServiceRequestPriorityResource($serviceRequestPriority) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestPriorityRequest $request
     * @param ServiceRequestPriority $serviceRequestPriority
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestPriorityRequest $request, ServiceRequestPriority $serviceRequestPriority): JsonResponse
    {
        $this->authorize('delete', $serviceRequestPriority);

        try {

            $serviceRequestPriority->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequestPriority->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestPriorityRequest $request
     * @param ServiceRequestPriority $serviceRequestPriority
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestPriorityRequest $request, ServiceRequestPriority $serviceRequestPriority): JsonResponse
    {
        $this->authorize('update', $serviceRequestPriority);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequestPriority->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestPriorityResource($serviceRequestPriority), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
