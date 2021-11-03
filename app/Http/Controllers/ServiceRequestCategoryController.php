<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestCategoryRequest;
use App\Http\Resources\Collections\ServiceRequestCategoryResourceCollection;
use App\Http\Resources\ServiceRequestCategoryResource;
use App\Models\ServiceRequestCategory;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestCategoryRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestCategoryRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequestCategory::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = ServiceRequestCategory::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = ServiceRequestCategory::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestCategoryResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestCategoryRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceRequestCategory::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = ServiceRequestCategory::create($data);

            return response()->json(new ServiceRequestCategoryResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequestCategory $serviceRequestCategory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequestCategory $serviceRequestCategory): JsonResponse
    {
        $this->authorize('view', $serviceRequestCategory);

        return response()->json(new ServiceRequestCategoryResource($serviceRequestCategory), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestCategoryRequest $request
     * @param ServiceRequestCategory $serviceRequestCategory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestCategoryRequest $request, ServiceRequestCategory $serviceRequestCategory): JsonResponse
    {
        $this->authorize('update', $serviceRequestCategory);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $serviceRequestCategory->update($data);

            return response()->json(new ServiceRequestCategoryResource($serviceRequestCategory) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestCategoryRequest $request
     * @param ServiceRequestCategory $serviceRequestCategory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestCategoryRequest $request, ServiceRequestCategory $serviceRequestCategory): JsonResponse
    {
        $this->authorize('delete', $serviceRequestCategory);

        try {

            $serviceRequestCategory->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequestCategory->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestCategoryRequest $request
     * @param ServiceRequestCategory $serviceRequestCategory
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestCategoryRequest $request, ServiceRequestCategory $serviceRequestCategory): JsonResponse
    {
        $this->authorize('update', $serviceRequestCategory);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequestCategory->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestCategoryResource($serviceRequestCategory), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
