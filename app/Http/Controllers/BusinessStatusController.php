<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusinessStatusRequest;
use App\Http\Resources\BusinessStatusResource;
use App\Http\Resources\Collections\BusinessStatusResourceCollection;
use App\Models\BusinessStatus;
use Illuminate\Http\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

class BusinessStatusController extends Controller
{

    public function index(BusinessStatusRequest $request): JsonResponse
    {
        $this->authorize('viewAny', BusinessStatus::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = BusinessStatus::select(
                'id',
                'code',
                'display',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = BusinessStatus::select(
                'id',
                'code',
                'display',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new BusinessStatusResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }


    public function store(BusinessStatusRequest $request): JsonResponse
    {
        $this->authorize('create', BusinessStatus::class);

        $data = array_merge($request->validated(),
            [
                'authored_on' => auth()->id(),
            ]);
        try {

            $model = BusinessStatus::create($data);

            return response()->json(new BusinessStatusResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(BusinessStatus $businessStatus): JsonResponse
    {
        $this->authorize('view', $businessStatus);

        return response()->json(new BusinessStatusResource($businessStatus), Response::HTTP_OK);
    }

    public function update(BusinessStatusRequest $request, BusinessStatus $businessStatus): JsonResponse
    {
        $this->authorize('update', $businessStatus);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $businessStatus->update($data);

            return response()->json(new BusinessStatusResource($businessStatus) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(BusinessStatusRequest $request, BusinessStatus $businessStatus): JsonResponse
    {
        $this->authorize('delete', $businessStatus);

        try {

            $businessStatus->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $businessStatus->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeActiveAttribute(BusinessStatusRequest $request, BusinessStatus $businessStatus): JsonResponse
    {
        $this->authorize('update', $businessStatus);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $businessStatus->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new BusinessStatusResource($businessStatus), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
