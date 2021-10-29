<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequestObservationCodeRequest;
use App\Http\Resources\collections\ServiceRequestObservationCodeResourceCollection;
use App\Http\Resources\ServiceRequestObservationCodeResource;
use App\Models\ServiceRequestObservationCode;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestObservationCodeController extends Controller
{
    /**
     * @param ServiceRequestObservationCodeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ServiceRequestObservationCodeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', ServiceRequestObservationCode::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = ServiceRequestObservationCode::select(
                'id',
                'name',
                'slug',
                'loinc_num',
                'container_id',
                'analyte_id',
                'specimen_code_id',
                'location_id',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else if ($request->input('letter')) {

            $items = ServiceRequestObservationCode::select(
                'id',
                'name',
                'loinc_num',
                'container_id',
                'analyte_id',
                'specimen_code_id',
                'location_id',
                'slug',
                'active',
            )->where('name', 'like', $request->input('letter') . "%")
                ->orderBy('id')
                ->get();

        } else {
            $items = ServiceRequestObservationCode::select(
                'id',
                'name',
                'loinc_num',
                'container_id',
                'analyte_id',
                'specimen_code_id',
                'location_id',
                'slug',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new ServiceRequestObservationCodeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param ServiceRequestObservationCodeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @author ELABNOTE
     */
    public function store(ServiceRequestObservationCodeRequest $request): JsonResponse
    {

        $this->authorize('create', ServiceRequestObservationCode::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
                'slug' => Str::slug($request->name)
            ]);
        try {

            $model = ServiceRequestObservationCode::create($data);

            return response()->json(new ServiceRequestObservationCodeResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestObservationCode $serviceRequestObservationCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(ServiceRequestObservationCode $serviceRequestObservationCode): JsonResponse
    {
        $this->authorize('view', $serviceRequestObservationCode);

        return response()->json(new ServiceRequestObservationCodeResource($serviceRequestObservationCode), Response::HTTP_OK);
    }

    /**
     * @param ServiceRequestObservationCodeRequest $request
     * @param ServiceRequestObservationCode $serviceRequestObservationCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ServiceRequestObservationCodeRequest $request, ServiceRequestObservationCode $serviceRequestObservationCode): JsonResponse
    {
        $this->authorize('update', $serviceRequestObservationCode);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
                'slug' => Str::slug($request->name)
            ]);

        try {
            $serviceRequestObservationCode->update($data);

            return response()->json(new ServiceRequestObservationCodeResource($serviceRequestObservationCode), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param ServiceRequestObservationCodeRequest $request
     * @param ServiceRequestObservationCode $serviceRequestObservationCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(ServiceRequestObservationCodeRequest $request, ServiceRequestObservationCode $serviceRequestObservationCode): JsonResponse
    {
        $this->authorize('delete', $serviceRequestObservationCode);

        try {

            $serviceRequestObservationCode->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $serviceRequestObservationCode->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * @param ServiceRequestObservationCodeRequest $request
     * @param ServiceRequestObservationCode $serviceRequestObservationCode
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(ServiceRequestObservationCodeRequest $request, ServiceRequestObservationCode $serviceRequestObservationCode): JsonResponse
    {
        $this->authorize('update', $serviceRequestObservationCode);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $serviceRequestObservationCode->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new ServiceRequestObservationCodeResource($serviceRequestObservationCode), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function searchByParams(Request $request): JsonResponse
    {

        if ($request->slug) {
            $serviceRequestObservationCode = $this->findBySlug($request->slug);

            return response()->json(new ServiceRequestObservationCodeResource($serviceRequestObservationCode), Response::HTTP_OK);
        }

        return response()->json([], Response::HTTP_OK);
    }

    private function findBySlug($slug)
    {
        return ServiceRequestObservationCode::where('slug', $slug)->first();
    }


    public function publicIndex(ServiceRequestObservationCodeRequest $request): JsonResponse
    {

        if ($request->input('letter')) {
            $items = ServiceRequestObservationCode::select(
                'id',
                'name',
                'loinc_num',
                'container_id',
                'analyte_id',
                'specimen_code_id',
                'location_id',
                'slug',
                'active',
            )
                ->where('name', 'like', Str::upper($request->input('letter')) . "%")
                ->orderBy('id')
                ->get();

            $collection = new ServiceRequestObservationCodeResourceCollection($items);
            return
                response()
                    ->json($collection->response()->getData(true), Response::HTTP_OK);

        } else if ($request->input('slug')) {
            $serviceRequestObservationCode = $this->findBySlug($request->input('slug'));

            return response()->json(new ServiceRequestObservationCodeResource($serviceRequestObservationCode), Response::HTTP_OK);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
