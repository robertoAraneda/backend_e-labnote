<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObservationServiceRequestRequest;
use App\Http\Resources\collections\ObservationServiceRequestResourceCollection;
use App\Http\Resources\ObservationServiceRequestResource;
use App\Models\ServiceRequestObservation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class ObservationServiceRequestController extends Controller
{
    /**
     * @param ObservationServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ObservationServiceRequestRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequestObservation::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = ServiceRequestObservation::select(
                'id',
                'name',
                'slug',
                'specimen_id',
                'analyte_id',
                'active',
            )
                ->with(['specimen', 'analyte'])
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = ServiceRequestObservation::select(
                'id',
                'name',
                'slug',
                'specimen_id',
                'analyte_id',
                'active',
            )
                ->with(['specimen', 'analyte'])
                ->orderBy('id')
                ->get();
        }
        $collection = new ObservationServiceRequestResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param ObservationServiceRequestRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @author ELABNOTE
     */
    public function store(ObservationServiceRequestRequest $request): JsonResponse
    {

        $this->authorize('create', ServiceRequestObservation::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
                'slug' => Str::slug($request->name)
            ]);
        try {

            $model = ServiceRequestObservation::create($data);

            return response()->json(new ObservationServiceRequestResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestObservation $observationServiceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequestObservation $observationServiceRequest): JsonResponse
    {
        $this->authorize('view', $observationServiceRequest);

        return response()->json(new ObservationServiceRequestResource($observationServiceRequest), Response::HTTP_OK);
    }

    /**
     * @param ObservationServiceRequestRequest $request
     * @param ServiceRequestObservation $observationServiceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ObservationServiceRequestRequest $request, ServiceRequestObservation $observationServiceRequest): JsonResponse
    {
        $this->authorize('update', $observationServiceRequest);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
                'slug' => Str::slug($request->name)
            ]);

        try {
            $observationServiceRequest->update($data);

            return response()->json(new ObservationServiceRequestResource($observationServiceRequest) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ObservationServiceRequestRequest $request
     * @param ServiceRequestObservation $observationServiceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ObservationServiceRequestRequest $request, ServiceRequestObservation $observationServiceRequest): JsonResponse
    {
        $this->authorize('delete', $observationServiceRequest);

        try {

            $observationServiceRequest->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $observationServiceRequest->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param ObservationServiceRequestRequest $request
     * @param ServiceRequestObservation $observationServiceRequest
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ObservationServiceRequestRequest $request, ServiceRequestObservation $observationServiceRequest): JsonResponse
    {
        $this->authorize('update', $observationServiceRequest);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $observationServiceRequest->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ObservationServiceRequestResource($observationServiceRequest), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchByParams(Request $request): JsonResponse
    {

        if($request->slug){
            $observationServiceRequest = $this->findBySlug($request->slug);

            return response()->json(new ObservationServiceRequestResource($observationServiceRequest), Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }

    private function findBySlug($slug){
        return ServiceRequestObservation::where('slug', $slug)->first();
    }
}
