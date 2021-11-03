<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentStatusRequest;
use App\Http\Resources\AppointmentStatusResource;
use App\Http\Resources\Collections\AppointmentStatusResourceCollection;
use App\Models\AppointmentStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentStatusController extends Controller
{

    public function index(AppointmentStatusRequest $request): JsonResponse
    {
        $this->authorize('viewAny', AppointmentStatus::class);

        $page = $request->input('page');

        if(isset($page)){
            $items = AppointmentStatus::select(
                'id',
                'code',
                'display',
            )
                ->orderBy('id')
                ->paginate($request->getPaginate());
        }else{
            $items = AppointmentStatus::select(
                'id',
                'code',
                'display',
            )
                ->orderBy('id')
                ->get();
        }
        $collection = new AppointmentStatusResourceCollection($items);
        return
            response()
                ->json($collection->response()->getData(true), Response::HTTP_OK);
    }


    public function store(AppointmentStatusRequest $request): JsonResponse
    {
        $this->authorize('create', AppointmentStatus::class);

        $data = array_merge($request->validated(),
            [
                'created_user_id' => auth()->id(),
                'created_user_ip' => $request->ip(),
            ]);
        try {

            $model = AppointmentStatus::create($data);

            return response()->json(new AppointmentStatusResource($model) , Response::HTTP_CREATED);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(AppointmentStatus $appointmentStatus): JsonResponse
    {
        $this->authorize('view', $appointmentStatus);

        return response()->json(new AppointmentStatusResource($appointmentStatus), Response::HTTP_OK);
    }

    public function update(AppointmentStatusRequest $request, AppointmentStatus $appointmentStatus): JsonResponse
    {
        $this->authorize('update', $appointmentStatus);

        $data = array_merge($request->validated(),
            [
                'updated_user_id' => auth()->id(),
                'updated_user_ip' => $request->ip(),
            ]);

        try {
            $appointmentStatus->update($data);

            return response()->json(new AppointmentStatusResource($appointmentStatus) , Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(AppointmentStatusRequest $request, AppointmentStatus $appointmentStatus): JsonResponse
    {
        $this->authorize('delete', $appointmentStatus);

        try {

            $appointmentStatus->update([
                'deleted_user_id' => auth()->id(),
                'deleted_user_ip' => $request->ip()
            ]);

            $appointmentStatus->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeActiveAttribute(AppointmentStatusRequest $request, AppointmentStatus $appointmentStatus): JsonResponse
    {
        $this->authorize('update', $appointmentStatus);

        $status = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        try {
            $appointmentStatus->update(['active' => $status, 'updated_user_id' => auth()->id()]);

            return response()->json(new AppointmentStatusResource($appointmentStatus), Response::HTTP_OK);
        }catch (\Exception $ex){
            return response()->json($ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
