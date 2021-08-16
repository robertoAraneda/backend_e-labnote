<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestRequest;
use App\Http\Resources\collections\ServiceRequestResourceCollection;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequest::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = ServiceRequest::select(
                'id',
                'note',
                'occurrence',
                'requisition',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = ServiceRequest::select(
                'id',
                'note',
                'occurrence',
                'requisition',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ServiceRequestRequest $request)
    {
        $this->authorize('create', ServiceRequest::class);

        $paramsValidated = (object) $request->validated();

        try {
            DB::beginTransaction();

            $serviceRequest = ServiceRequest::create(
                array_merge($request->validated(),
                [
                    'created_user_id' => auth()->id(),
                    'created_user_ip' => $request->ip(),
                ]));

            $specimensCollection = collect($paramsValidated->specimens);

            $specimens = $specimensCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $serviceRequest->specimens()->createMany($specimens);

            $observationsCollection = collect($paramsValidated->observations);

            $observations = $observationsCollection->map(function ($item) use ($request) {

                return array_merge($item,
                    [
                        'created_user_id' => auth()->id(),
                        'created_user_ip' => $request->ip()
                    ]);
            });

            $serviceRequest->observations()->createMany($observations);


            DB::commit();

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_CREATED);
        } catch (\Exception $ex) {

            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('view', $serviceRequest);

        return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('update', $serviceRequest);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $serviceRequest->update($data);

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('delete', $serviceRequest);

        try {

            $serviceRequest->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequest->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestRequest $request
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestRequest $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $this->authorize('update', $serviceRequest);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequest->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestResource($serviceRequest), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function observations(ServiceRequest $serviceRequest): JsonResponse
    {
        $observations= $serviceRequest->observations()->active()->orderBy('id')->get();

        $collection = \App\Http\Resources\collections\ServiceRequestResource::collection($observations);

        return response()->json($collection, 200);
    }


    /**
     * @param ServiceRequest $serviceRequest
     * @return JsonResponse
     */
    public function specimens(ServiceRequest $serviceRequest): JsonResponse
    {
        $specimens = $serviceRequest->specimens()->active()->orderBy('id')->get();

        $collection = \App\Http\Resources\collections\ServiceRequestResource::collection($specimens);

        return response()->json($collection, 200);
    }

}