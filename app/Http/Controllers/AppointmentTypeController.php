<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentTypeRequest;
use App\Http\Resources\AppointmentTypeResource;
use App\Http\Resources\collections\AppointmentTypeResourceCollection;
use App\Models\AppointmentType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppointmentTypeController extends Controller
{

    public function index(AppointmentTypeRequest $request): JsonResponse
    {
        $this->authorize('viewAny', AppointmentType::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = AppointmentType::select(
                'id',
                'code',
                'display',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = AppointmentType::select(
                'id',
                'code',
                'display',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new AppointmentTypeResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }


    public function store(AppointmentTypeRequest $request): JsonResponse
    {
        $this->authorize('create', AppointmentType::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = AppointmentType::create($data);

            return response()->json(new AppointmentTypeResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(AppointmentType $appointmentType): JsonResponse
    {
        $this->authorize('view', $appointmentType);

        return response()->json(new AppointmentTypeResource($appointmentType), Response::HTTP_OK);
    }

    public function update(AppointmentTypeRequest $request, AppointmentType $appointmentType): JsonResponse
    {
        $this->authorize('update', $appointmentType);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $appointmentType->update($data);

            return response()->json(new AppointmentTypeResource($appointmentType) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(AppointmentTypeRequest $request, AppointmentType $appointmentType): JsonResponse
    {
        $this->authorize('delete', $appointmentType);

        try {

            $appointmentType->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $appointmentType->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeActiveAttribute(AppointmentTypeRequest $request, AppointmentType $appointmentType): JsonResponse
    {
        $this->authorize('update', $appointmentType);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $appointmentType->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new AppointmentTypeResource($appointmentType), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
