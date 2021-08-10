<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestIntentRequest;
use App\Http\Resources\collections\ServiceRequestIntentResourceCollection;
use App\Http\Resources\ServiceRequestIntentResource;
use App\Models\ServiceRequestIntent;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestIntentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestIntentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestIntentRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequestIntent::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = ServiceRequestIntent::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = ServiceRequestIntent::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestIntentResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestIntentRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestIntentRequest $request): JsonResponse
    {
        $this->authorize('create', ServiceRequestIntent::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = ServiceRequestIntent::create($data);

            return response()->json(new ServiceRequestIntentResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequestIntent $serviceRequestIntent
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequestIntent $serviceRequestIntent): JsonResponse
    {
        $this->authorize('view', $serviceRequestIntent);

        return response()->json(new ServiceRequestIntentResource($serviceRequestIntent), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestIntentRequest $request
     * @param ServiceRequestIntent $serviceRequestIntent
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestIntentRequest $request, ServiceRequestIntent $serviceRequestIntent): JsonResponse
    {
        $this->authorize('update', $serviceRequestIntent);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $serviceRequestIntent->update($data);

            return response()->json(new ServiceRequestIntentResource($serviceRequestIntent) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestIntentRequest $request
     * @param ServiceRequestIntent $serviceRequestIntent
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestIntentRequest $request, ServiceRequestIntent $serviceRequestIntent): JsonResponse
    {
        $this->authorize('delete', $serviceRequestIntent);

        try {

            $serviceRequestIntent->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequestIntent->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestIntentRequest $request
     * @param ServiceRequestIntent $serviceRequestIntent
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestIntentRequest $request, ServiceRequestIntent $serviceRequestIntent): JsonResponse
    {
        $this->authorize('update', $serviceRequestIntent);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequestIntent->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestIntentResource($serviceRequestIntent), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
