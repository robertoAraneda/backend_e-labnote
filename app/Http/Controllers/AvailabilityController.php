<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Http\Resources\AvailabilityResource;
use App\Http\Resources\collections\AvailabilityResourceCollection;
use App\Http\Resources\WorkareaResource;
use App\Models\Availability;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AvailabilityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(AvailabilityRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Availability::class);

        $page = $request->input('page');

        if (isset($page)) {
            $items = Availability::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        } else {
            $items = Availability::select(
                'id',
                'name',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new AvailabilityResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AvailabilityRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(AvailabilityRequest $request): JsonResponse
    {
        $this->authorize('create', Availability::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = Availability::create($data);

            return response()->json(new AvailabilityResource($model), Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Availability $availability
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Availability $availability): JsonResponse
    {
        $this->authorize('view', $availability);

        return response()->json(new AvailabilityResource($availability), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AvailabilityRequest $request
     * @param Availability $availability
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(AvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $this->authorize('update', $availability);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $availability->update($data);

            return response()->json(new AvailabilityResource($availability), Response::HTTP_OK);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param AvailabilityRequest $request
     * @param Availability $availability
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(AvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $this->authorize('delete', $availability);

        try {

            $availability->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $availability->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param AvailabilityRequest $request
     * @param Availability $availability
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(AvailabilityRequest $request, Availability $availability): JsonResponse
    {
        $this->authorize('update', $availability);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $availability->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new AvailabilityResource($availability), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
