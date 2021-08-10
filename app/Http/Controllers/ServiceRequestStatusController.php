<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestStatusRequest;
use App\Http\Resources\collections\ServiceRequestStatusResourceCollection;
use App\Http\Resources\ServiceRequestStatusResource;
use App\Models\ServiceRequestStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestStatusRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestStatusRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequestStatus::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = ServiceRequestStatus::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = ServiceRequestStatus::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestStatusResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestStatusRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestStatusRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceRequestStatus::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = ServiceRequestStatus::create($data);

            return response()->json(new ServiceRequestStatusResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequestStatus $serviceRequestStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequestStatus $serviceRequestStatus): JsonResponse
    {
        $this->authorize('view', $serviceRequestStatus);

        return response()->json(new ServiceRequestStatusResource($serviceRequestStatus), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestStatusRequest $request
     * @param ServiceRequestStatus $serviceRequestStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestStatusRequest $request, ServiceRequestStatus $serviceRequestStatus): JsonResponse
    {
        $this->authorize('update', $serviceRequestStatus);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $serviceRequestStatus->update($data);

            return response()->json(new ServiceRequestStatusResource($serviceRequestStatus) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestStatusRequest $request
     * @param ServiceRequestStatus $serviceRequestStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestStatusRequest $request, ServiceRequestStatus $serviceRequestStatus): JsonResponse
    {
        $this->authorize('delete', $serviceRequestStatus);

        try {

            $serviceRequestStatus->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequestStatus->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestStatusRequest $request
     * @param ServiceRequestStatus $serviceRequestStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestStatusRequest $request, ServiceRequestStatus $serviceRequestStatus): JsonResponse
    {
        $this->authorize('update', $serviceRequestStatus);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequestStatus->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestStatusResource($serviceRequestStatus), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
