<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecimenStatusRequest;
use App\Http\Resources\collections\SpecimenStatusResourceCollection;
use App\Http\Resources\SpecimenStatusResource;
use App\Models\SpecimenStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SpecimenStatusController extends Controller
{
    /**
     * @param SpecimenStatusRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(SpecimenStatusRequest $request): JsonResponse
    {
        $this->authorize('viewAny', SpecimenStatus::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = SpecimenStatus::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = SpecimenStatus::select(
                'id',
                'code',
                'display',
                'active',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new SpecimenStatusResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }

    /**
     * @param SpecimenStatusRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @author ELABNOTE
     */
    public function store(SpecimenStatusRequest $request): JsonResponse
    {
        $this->authorize('create', SpecimenStatus::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = SpecimenStatus::create($data);

            return response()->json(new SpecimenStatusResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenStatus $specimenStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(SpecimenStatus $specimenStatus): JsonResponse
    {
        $this->authorize('view', $specimenStatus);

        return response()->json(new SpecimenStatusResource($specimenStatus), Response::HTTP_OK);
    }

    /**
     * @param SpecimenStatusRequest $request
     * @param SpecimenStatus $specimenStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(SpecimenStatusRequest $request, SpecimenStatus $specimenStatus): JsonResponse
    {
        $this->authorize('update', $specimenStatus);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $specimenStatus->update($data);

            return response()->json(new SpecimenStatusResource($specimenStatus) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenStatusRequest $request
     * @param SpecimenStatus $specimenStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(SpecimenStatusRequest $request, SpecimenStatus $specimenStatus): JsonResponse
    {
        $this->authorize('delete', $specimenStatus);

        try {

            $specimenStatus->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $specimenStatus->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param SpecimenStatusRequest $request
     * @param SpecimenStatus $specimenStatus
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function changeActiveAttribute(SpecimenStatusRequest $request, SpecimenStatus $specimenStatus): JsonResponse
    {
        $this->authorize('update', $specimenStatus);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $specimenStatus->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new SpecimenStatusResource($specimenStatus), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
